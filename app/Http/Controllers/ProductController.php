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

            return view('menus.product.add-product', compact('mode', 'categories', 'brands', 'subCategories', 'taxes','units'));
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

                    'sku'             => 'nullable|string|max:255|unique:products,sku',

                    'sub_category_id' => 'required|exists:sub_categories,id',
                    'description'     => 'nullable|string',

                    'unit_id'         => 'required|exists:units,id',
                    'unit_value'      => 'required|numeric|min:0.01',

                    'base_price'      => 'required|numeric|min:1',

                    // ✅ FULL VALIDATION HERE
                    'retailer_price'  => 'required|numeric|min:1|gte:base_price|lte:mrp',

                    'mrp'             => 'required|numeric|min:1',

                    'tax_id'          => 'required|exists:taxes,id',

                    'product_images'   => 'nullable|array',
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

            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories','units'));
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

            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories', 'taxes','units'));
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

                'sku'        => 'nullable|string|max:255',
                'description'=> 'nullable|string',

                'unit_id'    => 'required|exists:units,id',
                'unit_value' => 'required|numeric|min:0.01',

                'base_price'     => 'required|numeric|min:1',
                'retailer_price' => 'required|numeric|min:1',
                'mrp'            => 'required|numeric|min:1',

                'tax_id' => 'required|exists:taxes,id',

                'product_images'   => 'nullable|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'name.unique' => 'This product name already exists!',
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


}
