<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\SubCategory;
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

    public function getSubCategories($id)
    {
        $subcategories = SubCategory::where('category_id', $id)
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        if ($subcategories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No subcategories found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $subcategories
        ]);
    }

    public function getBrands()
    {
        $brands = Brand::select('id', 'name')->orderBy('id')->get();

        if ($brands->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No brands found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $brands
        ]);
    }

    public function getProductsBySubcategory($id)
    {
        $subcategory = SubCategory::find($id);

        if (!$subcategory) {
            return response()->json([
                'status' => false,
                'message' => 'Subcategory not found'
            ], 404);
        }

        $products = Product::where('sub_category_id', $id)
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
            'subcategory' => [
                'id' => $subcategory->id,
                'name' => $subcategory->name
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

    public function getSimilarProducts($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $similarProducts = Product::where('id', '!=', $product->id)
            ->where('stock', '>', 0)
            ->where(function ($query) use ($product) {
                $query->where('sub_category_id', $product->sub_category_id)
                    ->orWhere('category_id', $product->category_id);
            })
            ->orderByRaw(
                "CASE 
                WHEN sub_category_id = ? THEN 1
                WHEN category_id = ? THEN 2
                ELSE 3
            END",
                [$product->sub_category_id, $product->category_id]
            )
            ->limit(12)
            ->get()
            ->map(function ($item) {

                $images = is_string($item->product_images)
                    ? json_decode($item->product_images, true)
                    : $item->product_images;

                $item->image_urls = collect($images)->map(function ($img) {
                    return asset('storage/products/' . $img);
                });

                unset($item->product_images);

                return $item;
            });

        return response()->json([
            'status' => true,
            'product_id' => $id,
            'similar_products' => $similarProducts
        ]);
    }

    public function getProductDetails($id)
{
    $product = Product::with([
        'category:id,name',
        'subCategory:id,name,category_id',
        'brand:id,name'
    ])->find($id);

    if (!$product) {
        return response()->json([
            'status' => false,
            'message' => 'Product not found'
        ], 404);
    }

    // Build image URLs
    $images = is_string($product->product_images)
        ? json_decode($product->product_images, true)
        : $product->product_images;

    $imageUrls = collect($images)->map(function ($img) {
        return asset('storage/products/' . $img);
    });

    return response()->json([
        'status' => true,
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'sku' => $product->sku,

            'pricing' => [
                'base_price' => $product->base_price,
                'retailer_price' => $product->retailer_price,
                'mrp' => $product->mrp,
                'gst_percentage' => $product->gst_percentage,
            ],

            'stock' => $product->stock,
            'expiry_date' => $product->expiry_date,

            'images' => $imageUrls,

            'category' => $product->category,
            'sub_category' => $product->subCategory,
            'brand' => $product->brand,
        ]
    ]);
}

}
