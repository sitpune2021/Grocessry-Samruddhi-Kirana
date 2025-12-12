<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Product Store Request', ['request' => $request->all()]);

        // Validation
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

            // NEW: Validate product_images array (NO file upload)
            'product_images'      => 'nullable|array',
            'product_images.*'    => 'nullable|string'
        ]);

        // Save product_images JSON
        $validated['product_images'] = json_encode($request->product_images);

        $product = Product::create($validated);

        Log::info('Product Created Successfully', ['product' => $product]);

        return response()->json([
            'status'  => true,
            'message' => 'Product created successfully',
            'data'    => $product
        ], 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Log incoming request
            Log::info('Product Show Request Received', ['id' => $id]);

            // Find product
            $product = Product::find($id);
            if (!$product) {
                Log::warning("Product not found", ['id' => $id]);
                return response()->json([
                    'status'  => false,
                    'message' => 'Product not found'
                ], 404);
            }

            Log::info('Product Found', ['product' => $product]);

            // Return JSON response
            return response()->json([
                'status'  => true,
                'message' => 'Product fetched successfully',
                'data'    => $product
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Product Show Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Log incoming request
            Log::info('Product Update Request Received', [
                'id' => $id,
                'request' => $request->all()
            ]);

            // Find product
            $product = Product::find($id);
            if (!$product) {
                Log::warning("Product not found", ['id' => $id]);
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            Log::info('Product Found', ['product_before_update' => $product]);

            // Validation
            $validated = $request->validate([
                'category_id'     => 'required|exists:categories,id',
                'name'            => 'required|string|max:255',
                'sku'             => 'nullable|string|max:255',
                'description'     => 'nullable|string',
                'base_price'      => 'required|numeric|min:0',
                'retailer_price'  => 'required|numeric|min:0',
                'mrp'             => 'required|numeric|min:0',
                'gst_percentage'  => 'required|numeric|min:0|max:100',
                'stock'           => 'required|integer|min:0',
                'product_images'  => 'nullable|array',
                'product_images.*' => 'nullable|string'
            ]);

            // Convert product_images to JSON if provided
            if ($request->has('product_images')) {
                $validated['product_images'] = json_encode($request->product_images);
                Log::info('Product Images Updated', ['images' => $validated['product_images']]);
            }

            // Update product in DB
            $product->update($validated);

            Log::info('Product Updated Successfully', [
                'product_after_update' => $product
            ]);

            // Return proper JSON response
            return response()->json([
                'status'  => true,
                'message' => 'Product updated successfully',
                'data'    => $product
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Validation errors
            Log::error('Validation Failed', ['errors' => $ve->errors()]);
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Throwable $e) {
            // Other errors
            Log::error('Product Update Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error'  => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Log request
        Log::info('Delete Product Request', [
            'id' => $id
        ]);

        // Find Product
        $product = Product::find($id);

        if (!$product) {
            Log::warning("Product not found for delete", [
                'id' => $id
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'product not found'
            ], 404);
        }

        // Log before delete
        Log::info('product Found for Delete', [
            'product' => $product
        ]);

        // Perform soft delete
        $product->delete();

        // Log after delete
        Log::info('Product Soft Deleted Successfully', [
            'id' => $id,
            'deleted_at' => now()->toDateTimeString()
        ]);

        // JSON response
        return response()->json([
            'status'  => true,
            'message' => 'Product deleted successfully (soft deleted)'
        ], 200);
    }
}
