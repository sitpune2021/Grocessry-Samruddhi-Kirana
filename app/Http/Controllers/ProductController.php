<?php


namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Product Index Page Loaded', [
            'user_id' => auth()->id(),
        ]);

        try {
            $products = Product::with('category')->latest()->paginate(10);

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
            $categories = Category::select('id', 'name')->get();
            $brands = Brand::where('status', 1)
                ->orderBy('name')
                ->get();

            return view('menus.product.add-product', compact('mode', 'categories', 'brands'));
        } catch (\Throwable $e) {

            Log::error('Product Create Page Error', [
                'message' => $e->getMessage()
            ]);

            return redirect()->route('product.index')
                ->with('error', 'Unable to load product form');
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Product Store Request', [
            'request' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        try {
            $validated = $request->validate([
                'category_id'     => 'required|exists:categories,id',
                'brand_id'        => 'required',
                'name'            => 'required|string|max:255',
                'sku'             => 'nullable|string|max:255',
                'effective_date'  => 'required|date',
                'expiry_date'     => 'required|date|after_or_equal:effective_date',
                'description'     => 'nullable|string',
                'base_price'      => 'required|numeric',
                'retailer_price'  => 'required|numeric',
                'mrp'             => 'required|numeric',
                'gst_percentage'  => 'required|numeric',
                'stock'           => 'required|integer',
                'product_images'   => 'nullable|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
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


            $product = Product::create($validated);

            Log::info('Product Created Successfully', [
                'product_id' => $product->id
            ]);

            return redirect()->route('product.index')
                ->with('success', 'Product created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::warning('Product Store Validation Failed', [
                'errors' => $e->errors()
            ]);

            throw $e;
        } catch (\Throwable $e) {

            Log::error('Product Store Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while saving product');
        }
    }

    /**
     * Display the specified resource.
     */
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

            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands'));
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

            return view('menus.product.add-product', compact('product', 'mode', 'categories', 'brands'));
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
                'name'            => 'required|string|max:255',
                'sku'             => 'nullable|string|max:255',
                'description'     => 'nullable|string',
                'base_price'      => 'required|numeric',
                'retailer_price'  => 'required|numeric',
                'mrp'             => 'required|numeric',
                'gst_percentage'  => 'required|numeric',
                'stock'           => 'required|integer',
                'product_images'   => 'nullable|array',
                'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
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
            'user_id'    => auth()->id(),
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

    //get product by category
    public function getProductsByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)->get();

        return response()->json($products);
    }
}
