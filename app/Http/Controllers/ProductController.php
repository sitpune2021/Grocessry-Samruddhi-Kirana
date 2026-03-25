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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        Log::info('Product Index Page Loaded');
        try {
            $products = Product::with(['category', 'tax'])->latest()->paginate(20);

            $categories = Category::with([
                'subCategories.brands' => function ($q) {
                    $q->where('status', 1);
                }
            ])->get();
            $units = Unit::orderBy('name')->get();
            $taxes = Tax::all();
            return view('menus.product.index', compact('products', 'categories', 'units', 'taxes'));
        } catch (\Throwable $e) {
            Log::error('Product Index Error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->back()->with('error', 'Unable to load products');
        }
    }

    public function create()
    {
        try {
            Log::info('Product Create Page Loaded');
            $mode          = 'add';
            $categories    = Category::select('id', 'name')->orderBy('name')->get();
            $subCategories = collect();
            $brands        = collect();
            $taxes         = Tax::where('is_active', 1)->get();
            $units         = Unit::orderBy('name')->get();
            return view('menus.product.add-product', compact('mode', 'categories', 'brands', 'subCategories', 'taxes', 'units'));
        } catch (\Throwable $e) {
            Log::error('Product Create Page Error', ['message' => $e->getMessage()]);
            return redirect()->route('product.index')->with('error', 'Unable to load product form');
        }
    }

    public function store(Request $request)
    {
        Log::info('Product Store Request', ['request' => $request->all()]);
        try {
            $validated = $request->validate([
                'category_id'      => 'required|exists:categories,id',
                'brand_id'         => 'required|exists:brands,id',
                'name'             => 'required|string|max:255',
                'sku'              => 'nullable|string|max:255',
                'barcode'          => 'nullable|string|max:20',
                'sub_category_id'  => 'required|exists:sub_categories,id',
                'description'      => 'nullable|string',
                'unit_id'          => 'required|exists:units,id',
                'unit_value'       => 'required|numeric|min:0.01',
                'base_price'       => 'required|numeric|min:1',
                'retailer_price'   => 'required|numeric|gte:base_price',
                'mrp'              => 'required|numeric|min:1',
                'tax_id'           => 'required|exists:taxes,id',
                'product_images'   => 'required|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'retailer_price.gte' => 'Selling price cannot be less than Base Price',
            ]);

            if ($request->retailer_price > $request->mrp) {
                return back()->withInput()->with('error', 'Selling price cannot be greater than MRP');
            }

            $tax                         = Tax::findOrFail($request->tax_id);
            $gstPercent                  = $tax->gst ?? 0;
            $gstAmount                   = ($request->retailer_price * $gstPercent) / 100;
            $finalPrice                  = $request->retailer_price + $gstAmount;
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
                $validated['product_images'] = $imageNames;
            }

            $product = Product::create($validated);
            Log::info('Product Created Successfully', ['product_id' => $product->id]);
            return redirect()->route('product.index')->with('success', 'Product created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Product Store Validation Failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Product Store Error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return back()->withInput()->with('error', 'Something went wrong while saving product');
        }
    }

    public function show($id)
    {
        try {
            Log::info('Product View Request', ['id' => $id]);
            $product       = Product::with('tax')->findOrFail($id);
            $mode          = 'view';
            $categories    = Category::select('id', 'name')->get();
            $brands        = Brand::where('status', 1)->orderBy('name')->get();
            $subCategories = SubCategory::where('category_id', $product->category_id)->get();
            $units         = Unit::orderBy('name')->get();
            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories', 'units'));
        } catch (\Throwable $e) {
            Log::error('Product View Error', ['message' => $e->getMessage()]);
            return redirect()->route('product.index')->with('error', 'Unable to view product');
        }
    }

    public function edit($id)
    {
        try {
            Log::info('Product Edit Request', ['id' => $id]);
            $product = Product::find($id);
            if (!$product) {
                return redirect()->route('product.index')->with('error', 'Product not found');
            }
            $mode          = 'edit';
            $categories    = Category::select('id', 'name')->get();
            $brands        = Brand::where('sub_category_id', $product->sub_category_id)->select('id', 'name')->orderBy('name')->get();
            $taxes         = Tax::where('is_active', 1)->get();
            $subCategories = SubCategory::where('category_id', $product->category_id)->get();
            $units         = Unit::orderBy('name')->get();
            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories', 'taxes', 'units'));
        } catch (\Throwable $e) {
            Log::error('Product Edit Error', ['message' => $e->getMessage()]);
            return redirect()->route('product.index')->with('error', 'Unable to edit product');
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Product Update Request', ['id' => $id]);
        try {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->route('product.index')->with('error', 'Product not found');
            }

            $validated = $request->validate([
                'category_id'      => 'required|exists:categories,id',
                'brand_id'         => 'required|exists:brands,id',
                'sub_category_id'  => 'required|exists:sub_categories,id',
                'name'             => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($product->id)],
                'sku'              => 'nullable|string|max:255',
                'barcode'          => 'nullable|string|max:12',
                'description'      => 'nullable|string',
                'unit_id'          => 'required|exists:units,id',
                'unit_value'       => 'required|numeric|min:0.01',
                'base_price'       => 'required|numeric|min:1',
                'retailer_price'   => 'required|numeric|min:1',
                'mrp'              => 'required|numeric|min:1',
                'tax_id'           => 'required|exists:taxes,id',
                'product_images'   => 'nullable|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], ['name.unique' => 'This product name already exists!']);

            if ($request->retailer_price < $request->base_price) {
                return back()->withInput()->with('error', 'Selling price cannot be less than Base Price');
            }
            if ($request->retailer_price > $request->mrp) {
                return back()->withInput()->with('error', 'Selling price cannot be greater than MRP');
            }

            $tax                         = Tax::findOrFail($request->tax_id);
            $gstPercent                  = $tax->gst ?? 0;
            $gstAmount                   = ($request->retailer_price * $gstPercent) / 100;
            $finalPrice                  = $request->retailer_price + $gstAmount;
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
            Log::info('Product Updated Successfully', ['product_id' => $product->id]);
            return redirect()->route('product.index')->with('success', 'Product updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Product Update Validation Failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Product Update Error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return redirect()->back()->withInput()->with('error', 'Something went wrong while updating product');
        }
    }

    public function destroy($id)
    {
        Log::info('Product Delete Request', ['product_id' => $id, 'user_id' => Auth::id()]);
        try {
            $product = Product::find($id);
            if (!$product) {
                return redirect()->route('product.index')->with('error', 'Product not found');
            }
            $product->delete();
            Log::info('Product Deleted Successfully', ['product_id' => $id]);
            return redirect()->route('product.index')->with('success', 'Product deleted successfully');
        } catch (\Throwable $e) {
            Log::error('Product Delete Error', ['product_id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('product.index')->with('error', 'Something went wrong while deleting product');
        }
    }

    public function getCategories()
    {
        return response()->json(Category::select('id', 'name')->orderBy('name')->get());
    }

    public function getBrands($subCategoryId)
    {
        Log::info('Loading brands for sub-category', ['sub_category_id' => $subCategoryId]);
        return Brand::where('sub_category_id', $subCategoryId)->select('id', 'name')->orderBy('name')->get();
    }


    public function downloadSampleExcel(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'required|array',
            'unit' => 'required',
            'gst' => 'required'
        ]);

        $category = Category::findOrFail($request->category_id);
        $subcategory = SubCategory::findOrFail($request->subcategory_id);
        $brands = Brand::whereIn('id', $request->brand_id)->get();
        $unit = Unit::findOrFail($request->unit);
        $gst = Tax::findOrFail($request->gst);

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=product_sample.csv",
        ];

        return response()->stream(function () use ($category, $subcategory, $brands, $unit, $gst) {

            $file = fopen('php://output', 'w');
            echo "\xEF\xBB\xBF";

            fputcsv($file, [
                'Category',
                'SubCategory',
                'Brand',
                'Product Name',
                'Barcode',
                'Description',
                'Unit',
                'Unit Value',
                'Base Price',
                'Selling Price',
                'MRP',
                'GST',
                'Image Name'
            ]);

            foreach ($brands as $brand) {

                for ($i = 0; $i < 5; $i++) {
                    fputcsv($file, [
                        $category->name,
                        $subcategory->name,
                        $brand->name,
                        '',
                        '',
                        '',
                        $unit->name,
                        '',
                        '',
                        '',
                        '',
                        $gst->gst,
                        ''
                    ]);
                }
            }

            fclose($file);
        }, 200, $headers);
    }

    private function clean($value)
    {
        // Remove HTML
        $value = strip_tags($value);

        // Prevent Excel formula injection
        if (preg_match('/^[-+=@]/', $value)) {
            $value = "'" . $value;
        }

        return $value;
    }



    /**
     * Bulk Upload Products from Excel (XLSX/CSV)
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:5120',
        ]);

        Log::info('Bulk Upload Started', [
            'user_id' => auth()->id(),
            'file_name' => $request->file('csv_file')->getClientOriginalName()
        ]);

        $success = 0;
        $errors = [];

        $filePath = $request->file('csv_file')->getRealPath();
        $file = fopen($filePath, 'r');

        // ✅ Detect delimiter (comma or tab)
        $firstLine = fgets($file);
        $delimiter = str_contains($firstLine, "\t") ? "\t" : ",";

        rewind($file);

        // Skip header
        fgetcsv($file, 1000, $delimiter);

        $rowNumber = 1;

        while (($row = fgetcsv($file, 1000, $delimiter)) !== false) {

            $rowNumber++;

            try {
                if (empty(array_filter($row))) {
                    Log::warning("Row $rowNumber skipped: Empty row");
                    continue;
                }

                Log::info("Parsed Row $rowNumber", $row);

                // ✅ Map fields properly
                $categoryName    = trim(strip_tags($row[0] ?? ''));
                $subCategoryName = trim(strip_tags($row[1] ?? ''));
                $brandName       = trim(strip_tags($row[2] ?? ''));
                $productName     = trim(strip_tags($row[3] ?? ''));

                $unitName = trim($row[6] ?? '');
                $gstValue = trim($row[11] ?? '');

                if (!$productName) {
                    $msg = "Row $rowNumber: Product name missing";
                    Log::warning($msg);
                    $errors[] = $msg;
                    continue;
                }

                // ✅ Fetch relations
                $category = Category::where('name', $categoryName)->first();
                $subCategory = SubCategory::where('name', $subCategoryName)
                    ->where('category_id', $category?->id)
                    ->first();
                $brand = Brand::where('name', $brandName)->first();

                if (!$category || !$subCategory || !$brand) {
                    $msg = "Row $rowNumber: Invalid category/subcategory/brand";
                    Log::warning($msg, compact('categoryName', 'subCategoryName', 'brandName'));
                    $errors[] = $msg;
                    continue;
                }

                // ✅ Find Unit
                $unit = Unit::where('name', $unitName)->first();
                if (!$unit) {
                    $msg = "Row $rowNumber: Invalid Unit ($unitName)";
                    Log::warning($msg);
                    $errors[] = $msg;
                    continue;
                }

                // ✅ Find GST (Tax)
                $tax = Tax::where('gst', $gstValue)->first();
                if (!$tax) {
                    $msg = "Row $rowNumber: Invalid GST ($gstValue)";
                    Log::warning($msg);
                    $errors[] = $msg;
                    continue;
                }

                // ✅ Prices
                $basePrice = (float)($row[8] ?? 0);
                $retailerPrice = (float)($row[9] ?? 0);
                $mrp = (float)($row[10] ?? 0);

                // ✅ GST Calculation
                $gstAmount = ($retailerPrice * $tax->gst) / 100;
                $finalPrice = $retailerPrice + $gstAmount;

                // ✅ Duplicate check
                if (Product::where('name', $productName)
                    ->where('category_id', $category->id)
                    ->where('sub_category_id', $subCategory->id)
                    ->where('brand_id', $brand->id)
                    ->exists()
                ) {

                    $msg = "Row $rowNumber: Duplicate product";
                    Log::warning($msg);
                    $errors[] = $msg;
                    continue;
                }

                // ✅ Insert product
                Product::create([
                    'category_id'     => $category->id,
                    'sub_category_id' => $subCategory->id,
                    'brand_id'        => $brand->id,
                    'name'            => $productName,
                    'unit_id'         => $unit->id, // ✅ FIXED
                    'unit_value'      => (float)($row[7] ?? 0),
                    'sku'             => null,
                    'barcode'         => $row[4] ?? null,
                    'description'     => $row[5] ?? null,
                    'base_price'      => $basePrice,
                    'retailer_price'  => $retailerPrice,
                    'mrp'             => $mrp,
                    'tax_id'          => $tax->id, // ✅ FIXED
                    'gst_percentage'  => $tax->gst,
                    'gst_amount'      => $gstAmount,
                    'final_price'     => $finalPrice,
                    'stock'           => 0,
                ]);

                Log::info("Row $rowNumber inserted successfully");

                $success++;
            } catch (\Exception $e) {

                Log::error("Row $rowNumber failed", [
                    'error' => $e->getMessage(),
                    'line'  => $e->getLine()
                ]);

                $errors[] = "Row $rowNumber: " . $e->getMessage();
            }
        }

        fclose($file);

        Log::info('Bulk Upload Finished', [
            'success' => $success,
            'errors'  => count($errors)
        ]);

        // ✅ Generate error CSV
        if (!empty($errors)) {

            $fileName = 'error_report_' . time() . '.csv';
            $path = storage_path('app/public/' . $fileName);

            $f = fopen($path, 'w');

            foreach ($errors as $err) {
                fputcsv($f, [$err]);
            }

            fclose($f);

            return back()->with([
                'success' => "$success products uploaded",
                'error_file' => asset('storage/' . $fileName)
            ]);
        }

        return back()->with('success', "$success products uploaded successfully");
    }
}
