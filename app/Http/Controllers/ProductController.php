<?php


namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\SubCategory;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Exports\ProductSampleExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{


    public function index()
    {
        Log::info('Product Index Page Loaded');

        try {
            $products = Product::with(['category', 'tax'])
                ->latest()
                ->paginate(20);

            return view('menus.product.index', compact('products'));
        } catch (\Throwable $e) {

            Log::error('Product Index Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return redirect()->back()
                ->with('error', 'Unable to load products');
        }
    }

    public function create()
    {
        try {
            Log::info('Product Create Page Loaded');

            $mode = 'add';
            $categories = Category::select('id', 'name')
                ->orderBy('name')
                ->get();
            $subCategories = collect();
            $brands = collect();
            $taxes = Tax::where('is_active', 1)->get();
            $units = Unit::orderBy('name')->get();

            return view('menus.product.add-product', compact('mode', 'categories', 'brands', 'subCategories', 'taxes', 'units'));
        } catch (\Throwable $e) {

            Log::error('Product Create Page Error', [
                'message' => $e->getMessage()
            ]);

            return redirect()->route('product.index')
                ->with('error', 'Unable to load product form');
        }
    }

    public function store(Request $request)
    {
        Log::info('Product Store Request', [
            'request' => $request->all(),
        ]);

        try {

            $validated = $request->validate([
                'category_id'     => 'required|exists:categories,id',
                'brand_id'        => 'required|exists:brands,id',

                'name'            => 'required|string|max:255',

                'sku'             => 'nullable|string|max:255',
                'barcode'         => 'nullable|string|max:20',

                'sub_category_id' => 'required|exists:sub_categories,id',
                'description'     => 'nullable|string',

                'unit_id'         => 'required|exists:units,id',
                'unit_value'      => 'required|numeric|min:0.01',

                'base_price'      => 'required|numeric|min:1',

                // FULL VALIDATION HERE
                'retailer_price' => 'required|numeric|gte:base_price',
                'mrp'             => 'required|numeric|min:1',
                'tax_id'          => 'required|exists:taxes,id',

                'product_images'   => 'required|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            ], [
                'retailer_price.gte' => 'Selling price cannot be less than Base Price',
                'retailer_price.lte' => 'Selling price cannot be greater than MRP',
            ]);


            if ($request->retailer_price > $request->mrp) {
                return back()->withInput()
                    ->with('error', 'Selling price cannot be greater than MRP');
            }

            $tax = Tax::findOrFail($request->tax_id);
            $gstPercent = $tax->gst ?? 0;

            $gstAmount  = ($request->retailer_price * $gstPercent) / 100;
            $finalPrice = $request->retailer_price + $gstAmount;

            $validated['gst_percentage'] = $gstPercent;
            $validated['gst_amount']     = round($gstAmount, 2);
            $validated['final_price']    = round($finalPrice, 2);

            if ($request->hasFile('product_images')) {
                $imageNames = [];

                foreach ($request->file('product_images') as $image) {
                    $name = time() . '_' . $image->getClientOriginalName();
                    $image->storeAs('products', $name, 'public');
                    $imageNames[] = $name;
                }

                $validated['product_images'] = $imageNames; // JSON cast
            }

            $product = Product::create($validated);

            Log::info('Product Created Successfully', [
                'product_id' => $product->id,
            ]);

            return redirect()->route('product.index')
                ->with('success', 'Product created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::warning('Product Store Validation Failed', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (\Throwable $e) {

            Log::error('Product Store Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()->withInput()
                ->with('error', 'Something went wrong while saving product');
        }
    }

    public function show($id)
    {
        try {
            Log::info('Product View Request', ['id' => $id]);

            $product = Product::with('tax')->findOrFail($id);

            if (!$product) {
                Log::warning('Product Not Found', ['id' => $id]);
                return redirect()->route('product.index')
                    ->with('error', 'Product not found');
            }

            $mode = 'view'; // important for form disabling
            $categories = Category::select('id', 'name')->get();
            $brands = Brand::where('status', 1)
                ->orderBy('name')
                ->get();

            $subCategories = SubCategory::where('category_id', $product->category_id)->get();
            $units = Unit::orderBy('name')->get();

            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories', 'units'));
        } catch (\Throwable $e) {
            Log::error('Product View Error', ['message' => $e->getMessage()]);
            return redirect()->route('product.index')
                ->with('error', 'Unable to view product');
        }
    }

    public function edit($id)
    {
        try {
            Log::info('Product Edit Request', ['id' => $id]);

            $product = Product::find($id);

            if (!$product) {
                Log::warning('Product Not Found for Edit', ['id' => $id]);
                return redirect()->route('product.index')
                    ->with('error', 'Product not found');
            }

            $mode = 'edit';
            $categories = Category::select('id', 'name')->get();
            $brands = Brand::where('sub_category_id', $product->sub_category_id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            $taxes = Tax::where('is_active', 1)->get();
            $subCategories = SubCategory::where('category_id', $product->category_id)->get();
            $units = Unit::orderBy('name')->get();

            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories', 'taxes', 'units'));
        } catch (\Throwable $e) {

            Log::error('Product Edit Error', [
                'message' => $e->getMessage()
            ]);

            return redirect()->route('product.index')
                ->with('error', 'Unable to edit product');
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Product Update Request', [
            'id' => $id,
            'request' => $request->all(),
        ]);

        try {
            $product = Product::find($id);

            if (!$product) {
                Log::warning('Product Not Found for Update', ['id' => $id]);
                return redirect()->route('product.index')
                    ->with('error', 'Product not found');
            }

            $validated = $request->validate([
                'category_id'     => 'required|exists:categories,id',
                'brand_id'        => 'required|exists:brands,id',
                'sub_category_id' => 'required|exists:sub_categories,id',

                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('products', 'name')->ignore($product->id),
                ],

                'sku'               => 'nullable|string|max:255',
                'barcode'           => 'nullable|string|max:12',
                'description'       => 'nullable|string',

                'unit_id'           => 'required|exists:units,id',
                'unit_value'        => 'required|numeric|min:0.01',

                'base_price'        => 'required|numeric|min:1',
                'retailer_price'    => 'required|numeric|min:1',
                'mrp'               => 'required|numeric|min:1',

                'tax_id'            => 'required|exists:taxes,id',

                'product_images'    => 'nullable|array',
                'product_images.*'  => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'name.unique'       => 'This product name already exists!',
            ]);

            if ($request->retailer_price < $request->base_price) {
                return back()->withInput()
                    ->with('error', 'Selling price cannot be less than Base Price');
            }

            if ($request->retailer_price > $request->mrp) {
                return back()->withInput()
                    ->with('error', 'Selling price cannot be greater than MRP');
            }

            $tax = Tax::findOrFail($request->tax_id);
            $gstPercent = $tax->gst ?? 0;

            $gstAmount  = ($request->retailer_price * $gstPercent) / 100;
            $finalPrice = $request->retailer_price + $gstAmount;

            $validated['gst_percentage'] = $gstPercent;
            $validated['gst_amount']     = round($gstAmount, 2);
            $validated['final_price']    = round($finalPrice, 2);

            if ($request->hasFile('product_images')) {
                $imageNames = [];

                foreach ($request->file('product_images') as $image) {
                    $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                    $image->storeAs('products', $fileName, 'public');
                    $imageNames[] = $fileName;
                }

                $validated['product_images'] = $imageNames;
            }

            $product->update($validated);

            Log::info('Product Updated Successfully', [
                'product_id' => $product->id
            ]);

            return redirect()->route('product.index')
                ->with('success', 'Product updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::warning('Product Update Validation Failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Throwable $e) {

            Log::error('Product Update Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating product');
        }
    }

    public function destroy($id)
    {
        Log::info('Product Delete Request', [
            'product_id' => $id,
            'user_id'    => Auth::id(),
        ]);

        try {
            $product = Product::find($id);

            if (!$product) {
                Log::warning('Product Not Found for Delete', [
                    'product_id' => $id
                ]);

                return redirect()->route('product.index')
                    ->with('error', 'Product not found');
            }

            $product->delete();

            Log::info('Product Deleted Successfully', [
                'product_id' => $id
            ]);

            return redirect()->route('product.index')
                ->with('success', 'Product deleted successfully');
        } catch (\Throwable $e) {

            Log::error('Product Delete Error', [
                'product_id' => $id,
                'message'    => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
            ]);

            return redirect()->route('product.index')
                ->with('error', 'Something went wrong while deleting product');
        }
    }

    public function getCategories()
    {
        return response()->json(
            Category::select('id', 'name')
                ->orderBy('name')
                ->get()
        );
    }

    public function getBrands($subCategoryId)
    {
        Log::info('Loading brands for sub-category', [
            'sub_category_id' => $subCategoryId
        ]);

        return Brand::where('sub_category_id', $subCategoryId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }
    public function downloadSampleExcel()
    {
        return Excel::download(new ProductSampleExport, 'product_sample.xlsx');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Log::info('Product Bulk Upload Started', [
                'ip'         => $request->ip(),
                'file'       => $request->file('excel_file')->getClientOriginalName(),
                'user_id'    => auth()->id(),
            ]);

            $rows = Excel::toArray([], $request->file('excel_file'));
            $data = $rows[0] ?? [];
            array_shift($data); // header skip

            Log::info('Product Bulk Upload: Total Rows', ['count' => count($data)]);

            $successCount = 0;
            $skippedCount = 0;

            foreach ($data as $rowIndex => $row) {

                $categoryName    = trim($row[0] ?? '');
                $subCategoryName = trim($row[1] ?? '');
                $brandName       = trim($row[2] ?? '');
                $productName     = trim($row[3] ?? '');
                $barcode         = trim($row[4] ?? '');
                $description     = trim($row[5] ?? '');
                $unitLabel       = trim($row[6] ?? '');
                $unitValue       = trim($row[7] ?? '');
                $basePrice       = trim($row[8] ?? '');
                $sellingPrice    = trim($row[9] ?? '');
                $mrp             = trim($row[10] ?? '');
                $gstLabel        = trim($row[11] ?? '');
                $imageUrl        = trim($row[12] ?? '');

                $rowNum = $rowIndex + 2;

                Log::info("Product Bulk Upload: Processing Row $rowNum", [
                    'product_name'     => $productName,
                    'category'         => $categoryName,
                    'sub_category'     => $subCategoryName,
                    'brand'            => $brandName,
                    'unit'             => $unitLabel,
                    'base_price'       => $basePrice,
                    'selling_price'    => $sellingPrice,
                    'mrp'              => $mrp,
                    'gst'              => $gstLabel,
                    'has_image'        => !empty($imageUrl),
                ]);

                // Required fields check
                if (empty($productName) || empty($categoryName) || empty($subCategoryName) || empty($brandName)) {
                    Log::warning("Product Bulk Upload: Skipped — Required Fields Empty", [
                        'row'          => $rowNum,
                        'product_name' => $productName,
                        'category'     => $categoryName,
                        'sub_category' => $subCategoryName,
                        'brand'        => $brandName,
                    ]);
                    $skippedCount++;
                    continue;
                }

                // Category
                $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
                if (!$category) {
                    Log::warning("Product Bulk Upload: Skipped — Category Not Found", [
                        'row' => $rowNum,
                        'category' => $categoryName,
                    ]);
                    $skippedCount++;
                    continue;
                }

                // SubCategory
                $subCategory = SubCategory::whereRaw('LOWER(name) = ?', [strtolower($subCategoryName)])
                    ->where('category_id', $category->id)->first();
                if (!$subCategory) {
                    Log::warning("Product Bulk Upload: Skipped — SubCategory Not Found", [
                        'row' => $rowNum,
                        'sub_category' => $subCategoryName,
                        'category_id' => $category->id,
                    ]);
                    $skippedCount++;
                    continue;
                }

                // Brand
                $brand = Brand::whereRaw('LOWER(name) = ?', [strtolower($brandName)])
                    ->where('sub_category_id', $subCategory->id)->first();
                if (!$brand) {
                    Log::warning("Product Bulk Upload: Skipped — Brand Not Found", [
                        'row' => $rowNum,
                        'brand' => $brandName,
                        'sub_category_id' => $subCategory->id,
                    ]);
                    $skippedCount++;
                    continue;
                }

                // Unit
                preg_match('/^(.+?)\s*\(([^)]+)\)$/', $unitLabel, $unitMatch);
                $unitName = trim($unitMatch[1] ?? $unitLabel);
                $unit = \App\Models\Unit::whereRaw('LOWER(name) = ?', [strtolower($unitName)])->first();
                if (!$unit) {
                    Log::warning("Product Bulk Upload: Skipped — Unit Not Found", [
                        'row' => $rowNum,
                        'unit_label' => $unitLabel,
                        'parsed_unit' => $unitName,
                    ]);
                    $skippedCount++;
                    continue;
                }

                // GST
                preg_match('/^(.+?)\s*\(([0-9.]+)%\)$/', $gstLabel, $gstMatch);
                $gstName = trim($gstMatch[1] ?? $gstLabel);
                $tax = \App\Models\Tax::whereRaw('LOWER(name) = ?', [strtolower($gstName)])->first();
                if (!$tax) {
                    Log::warning("Product Bulk Upload: Skipped — GST/Tax Not Found", [
                        'row' => $rowNum,
                        'gst_label' => $gstLabel,
                        'parsed_gst' => $gstName,
                    ]);
                    $skippedCount++;
                    continue;
                }

                // Price validation
                if (!is_numeric($basePrice) || !is_numeric($sellingPrice) || !is_numeric($mrp)) {
                    Log::warning("Product Bulk Upload: Skipped — Invalid Price (non-numeric)", [
                        'row'           => $rowNum,
                        'base_price'    => $basePrice,
                        'selling_price' => $sellingPrice,
                        'mrp'           => $mrp,
                    ]);
                    $skippedCount++;
                    continue;
                }
                if ($sellingPrice < $basePrice || $sellingPrice > $mrp) {
                    Log::warning("Product Bulk Upload: Skipped — Price Range Invalid", [
                        'row'           => $rowNum,
                        'base_price'    => $basePrice,
                        'selling_price' => $sellingPrice,
                        'mrp'           => $mrp,
                        'reason'        => $sellingPrice < $basePrice ? 'selling < base' : 'selling > mrp',
                    ]);
                    $skippedCount++;
                    continue;
                }

                // Duplicate check
                if (Product::whereRaw('LOWER(name) = ?', [strtolower($productName)])->exists()) {
                    Log::warning("Product Bulk Upload: Skipped — Duplicate Product", [
                        'row' => $rowNum,
                        'product_name' => $productName,
                    ]);
                    $skippedCount++;
                    continue;
                }

                // GST Calculate
                $gstPercent = $tax->gst ?? 0;
                $gstAmount  = ($sellingPrice * $gstPercent) / 100;
                $finalPrice = $sellingPrice + $gstAmount;

                // SKU auto generate
                $sku = strtoupper(Str::slug($productName, '')) . rand(1000, 9999);

                // Image download
                $imageNames = [];
                if (!empty($imageUrl)) {
                    $savedImage = $this->downloadImage($imageUrl, 'products');
                    if ($savedImage) {
                        $imageNames[] = $savedImage;
                        Log::info("Product Bulk Upload: Image Saved", [
                            'row' => $rowNum,
                            'file' => $savedImage,
                        ]);
                    } else {
                        Log::warning("Product Bulk Upload: Image Download Failed", [
                            'row' => $rowNum,
                            'url' => $imageUrl,
                        ]);
                    }
                }

                Product::create([
                    'category_id'     => $category->id,
                    'sub_category_id' => $subCategory->id,
                    'brand_id'        => $brand->id,
                    'name'            => $productName,
                    'sku'             => $sku,
                    'barcode'         => $barcode ?: null,
                    'description'     => $description ?: null,
                    'unit_id'         => $unit->id,
                    'unit_value'      => $unitValue,
                    'base_price'      => $basePrice,
                    'retailer_price'  => $sellingPrice,
                    'mrp'             => $mrp,
                    'tax_id'          => $tax->id,
                    'gst_percentage'  => $gstPercent,
                    'gst_amount'      => round($gstAmount, 2),
                    'final_price'     => round($finalPrice, 2),
                    'product_images'  => !empty($imageNames) ? $imageNames : null,
                ]);

                Log::info("Product Bulk Upload: Product Created", [
                    'row'          => $rowNum,
                    'product_name' => $productName,
                    'sku'          => $sku,
                    'category_id'  => $category->id,
                    'brand_id'     => $brand->id,
                    'final_price'  => round($finalPrice, 2),
                ]);

                $successCount++;
            }

            Log::info('Product Bulk Upload Completed', [
                'success' => $successCount,
                'skipped' => $skippedCount,
                'total'   => $successCount + $skippedCount,
            ]);

            return redirect()->route('product.index')
                ->with('success', "{$successCount} products imported. {$skippedCount} skipped.");
        } catch (\Exception $e) {
            Log::error('Product Bulk Upload Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    // ── Reusable image download helper ───────────────────────────────────────
    private function downloadImage(string $url, string $folder): ?string
    {
        // Base64 Data URI
        if (str_starts_with($url, 'data:image')) {
            preg_match('/data:image\/(\w+);base64,(.+)/', $url, $matches);
            if (count($matches) === 3) {
                $imageData = base64_decode($matches[2]);
                if ($imageData !== false) {
                    $fileName = time() . '_' . uniqid() . '.' . $matches[1];
                    Storage::disk('public')->put($folder . '/' . $fileName, $imageData);
                    return $fileName;
                }
            }
            return null;
        }

        // HTTP URL
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                $response = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
                if ($response->successful()) {
                    $ext      = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                    $fileName = time() . '_' . uniqid() . '.' . $ext;
                    Storage::disk('public')->put($folder . '/' . $fileName, $response->body());
                    return $fileName;
                }
            } catch (\Exception $e) {
                Log::warning("Image download failed", ['url' => $url, 'error' => $e->getMessage()]);
            }
        }

        return null;
    }
}
