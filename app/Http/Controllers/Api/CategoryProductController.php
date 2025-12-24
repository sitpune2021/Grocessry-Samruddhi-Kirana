<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;

class CategoryProductController extends Controller
{

    public function getCategories()
    {
        $categories = Category::select('id', 'name', 'slug')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }

    public function getProductsByCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $products = Product::where('category_id', $id)
            ->select(
                'id',
                'category_id',
                'name',
                'base_price',
                'retailer_price',
                'mrp',
                'gst_percentage',
                'stock',
                'product_images'
            )
            ->get()
            ->map(function ($product) {

                // Decode images if stored as JSON
                $images = is_string($product->product_images)
                    ? json_decode($product->product_images, true)
                    : $product->product_images;


                // Build full URLs
                $product->image_urls = collect($images)->map(function ($img) {
                    return asset('storage/products/' . $img);
                });

                // Optional: remove raw column
                unset($product->product_images);

                return $product;
            });

        return response()->json([
            'status' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name
            ],
            'products' => $products
        ]);
    }
    public function getProductsByBrand($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found'
            ], 404);
        }

        $products = Product::where('brand_id', $id)
            ->select(
                'id',
                'brand_id',
                'category_id',
                'name',
                'base_price',
                'retailer_price',
                'mrp',
                'gst_percentage',
                'stock',
                'product_images'
            )
            ->get()
            ->map(function ($product) {

                $images = is_string($product->product_images)
                    ? json_decode($product->product_images, true)
                    : $product->product_images;

                $product->image_urls = collect($images)->map(function ($img) {
                    return asset('storage/products/' . $img);
                });

                unset($product->product_images);

                return $product;
            });

        return response()->json([
            'status' => true,
            'brand' => [
                'id' => $brand->id,
                'name' => $brand->name
            ],
            'products' => $products
        ]);
    }
}
