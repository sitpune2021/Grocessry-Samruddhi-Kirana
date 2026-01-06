<?php


namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        Log::info('Product Index Page Loaded', [
            'user_id'      => $user->id,
            'warehouse_id' => $user->warehouse_id,
            'role_id'      => $user->role_id,
        ]);

        try {
            $products = Product::with('category')
                ->when($user->role_id != 1, function ($query) use ($user) {
                    // If NOT Super Admin → filter by warehouse
                    $query->where('warehouse_id', $user->warehouse_id);
                })
                ->latest()
                ->paginate(10);

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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            Log::info('Product Create Page Loaded');

            $mode = 'add';
            $brands = Brand::where('status', 1)->orderBy('name')->get();
            $categories = collect();
            $subCategories = collect();

            return view('menus.product.add-product', compact('mode', 'categories', 'brands', 'subCategories'));
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
    $user = Auth::user();

    Log::info('Product Store Request', [
        'user_id'      => $user->id,
        'warehouse_id' => $user->warehouse_id,
    ]);

    try {
        $validated = $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'brand_id'        => 'required|exists:brands,id',
            'name'            => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name'),
            ],
            'sku'             => 'nullable|string|max:255',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'description'     => 'nullable|string',

            'base_price'      => 'required|numeric|min:1',
            'retailer_price'  => 'required|numeric|min:1',
            'mrp'             => 'required|numeric|min:1',
            'gst_percentage'  => 'required|numeric|min:0|max:100',

            'discount_type'   => 'nullable|in:flat,percentage',
            'discount_value'  => 'nullable|numeric|min:0',

            'product_images'   => 'nullable|array',
            'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.unique' => 'This product name already exists!',
        ]);

        if (!empty($validated['discount_type'])) {

            if (
                $validated['discount_type'] === 'percentage'
                && $validated['discount_value'] > 100
            ) {
                return back()->withInput()
                    ->with('error', 'Discount percentage cannot exceed 100');
            }

            if (
                $validated['discount_type'] === 'flat'
                && $validated['discount_value'] > $validated['mrp']
            ) {
                return back()->withInput()
                    ->with('error', 'Flat discount cannot exceed MRP');
            }

        } else {
            $validated['discount_value'] = 0;
        }

        if ($request->hasFile('product_images')) {

            $imageNames = [];

            foreach ($request->file('product_images') as $image) {
                $name = time().'_'.$image->getClientOriginalName();
                $image->storeAs('products', $name, 'public');
                $imageNames[] = $name;
            }

            $validated['product_images'] = json_encode($imageNames);
        }

        $validated['warehouse_id'] = $user->warehouse_id;

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

            $product = Product::find($id);

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

            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories'));
        } catch (\Throwable $e) {
            Log::error('Product View Error', ['message' => $e->getMessage()]);
            return redirect()->route('product.index')
                ->with('error', 'Unable to view product');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
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
            $brands = Brand::where('status', 1)
                ->orderBy('name')
                ->get();

            $subCategories = SubCategory::where('category_id', $product->category_id)->get();

            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands', 'subCategories'));
        } catch (\Throwable $e) {

            Log::error('Product Edit Error', [
                'message' => $e->getMessage()
            ]);

            return redirect()->route('product.index')
                ->with('error', 'Unable to edit product');
        }
    }

    /**
     * Update the specified resource in storage.
     */
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
                'brand_id'        => 'required',
                'name'            => ['required', 'string', 'max:255', Rule::unique('products', 'name')],
                'sku'             => 'nullable|string|max:255',
                // 'effective_date'  => 'required|date',
                // 'expiry_date'     => 'required|date|after_or_equal:effective_date',
                'description'     => 'nullable|string',
                'base_price'      => 'required|numeric|min:1',
                'retailer_price'  => 'required|numeric|min:1',
                'mrp'             => 'required|numeric|min:1',
                'gst_percentage'  => 'required|numeric|min:0|max:100',
                // 'stock'           => 'required|integer',
                'product_images'   => 'nullable|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'name.unique' => 'This product name already exists!',
            ]);
            if ($request->hasFile('product_images')) {

                $imageNames = [];

                foreach ($request->file('product_images') as $image) {

                    $originalName = $image->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '_' . $originalName;

                    $image->storeAs('products', $fileName, 'public');

                    $imageNames[] = $fileName;
                }

                // store as JSON in DB
                $validated['product_images'] = json_encode($imageNames);
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
                'message' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating product');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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

    //get category by Brand

    public function getCategoriesByBrand($brandId)
    {

        return Category::where('brand_id', $brandId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }
}
