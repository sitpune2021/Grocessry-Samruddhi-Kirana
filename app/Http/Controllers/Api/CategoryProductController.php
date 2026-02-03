<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\WarehouseStock;

class CategoryProductController extends Controller
{

    public function getCategories()
    {
        try {
            $categories = Category::select('id', 'name', 'slug')
                ->orderBy('id')
                ->get();


            return response()->json([
                'status'  => true,
                'message' => $categories->isEmpty()
                    ? 'No categories found'
                    : 'Categories fetched successfully',
                'data'    => $categories
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function getSubCategoriesByCategory($id)
    {
        try {
            $subcategories = SubCategory::where('category_id', $id)
                ->select('id', 'name')
                ->orderBy('id')
                ->get();


            return response()->json([
                'status'  => true,
                'message' => $subcategories->isEmpty()
                    ? 'No subcategories found'
                    : 'Subcategories fetched successfully',
                'category_id' => (int) $id,
                'data'    => $subcategories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function getBrands()
    {
        try {
            $brands = Brand::select('id', 'name')
                ->orderBy('id')
                ->get();

            return response()->json([
                'status'  => true,
                'message' => $brands->isEmpty()
                    ? 'No brands found'
                    : 'Brands fetched successfully',
                'data'    => $brands
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function getProductsBySubcategory($id)
    {
        $subcategory = SubCategory::find($id);

        if (!$subcategory) {
            return response()->json([
                'status' => true,
                'message' => 'Subcategory not found',
                'data' => []
            ]);
        }

        $products = Product::where('sub_category_id', $id)
            ->get()
            ->map(function ($product) {

                // ✅ REAL STOCK from warehouse_stock
                $availableStock = WarehouseStock::where('product_id', $product->id)
                    ->sum('quantity');
                $discountPercent = 0;
                if ($product->mrp > 0 && $product->final_price < $product->mrp) {
                    $discountPercent = round(
                        (($product->mrp - $product->final_price) / $product->mrp) * 100
                    );
                }

                $images = is_string($product->product_images)
                    ? json_decode($product->product_images, true)
                    : $product->product_images;
                $discountPercent = 0;
                if ($product->mrp > $product->final_price) {
                    $discountPercent = round(
                        (($product->mrp - $product->final_price) / $product->mrp) * 100
                    );
                }

                return [
                    'id' => $product->id,
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                    'name' => $product->name,
                    'base_price' => $product->base_price,
                    'retailer_price' => $product->retailer_price,
                    'mrp' => $product->mrp,
                    'final_price' => $product->final_price,

                    'gst_percentage' => $product->gst_percentage,
                    'discount_percentage' => $discountPercent,
                    'discount_label' => $discountPercent > 0 ? $discountPercent . '% OFF' : null,
                    // ✅ FIX HERE
                    'stock' => $availableStock,
                    'quantity' => 1,
                    'max_quantity' => $availableStock,

                    'image_urls' => collect($images)->map(
                        fn($img) =>
                        asset('storage/products/' . $img)
                    )
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'subcategory' => [
                'id' => $subcategory->id,
                'name' => $subcategory->name
            ],
            'data' => $products
        ]);
    }

    public function getProductsByBrand($id)
    {
        try {
            $brand = Brand::find($id);

            if (!$brand) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Brand not found',
                    'data'    => []
                ], 200);
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
                    'final_price',
                    'product_images'
                )
                ->get()
                ->map(function ($product) {

                    /* 🔹 STOCK SET (REAL STOCK FROM WAREHOUSE) */
                    $stock = (int) WarehouseStock::where('product_id', $product->id)
                        ->sum('quantity');

                    /* 🔹 MAX STOCK SET (APP LIMIT) */
                    $maxStockLimit = 10; // you can change this
                    $product->stock = $stock;
                    $product->max_stock = min($stock, $maxStockLimit);
                    $discountPercent = 0;
                    if ($product->mrp > $product->final_price) {
                        $discountPercent = round(
                            (($product->mrp - $product->final_price) / $product->mrp) * 100
                        );
                    }

                    $product->discount_percentage = $discountPercent;
                    $product->discount_label = $discountPercent > 0
                        ? $discountPercent . '% OFF'
                        : null;

                    /* 🔹 IMAGE URL SET */
                    $images = is_string($product->product_images)
                        ? json_decode($product->product_images, true)
                        : $product->product_images;

                    $product->image_urls = collect($images)->map(function ($img) {
                        return asset('storage/products/' . $img);
                    })->values();

                    unset($product->product_images);

                    return $product;
                });

            return response()->json([
                'status'  => true,
                'message' => $products->isEmpty()
                    ? 'No products found for this brand'
                    : 'Products fetched successfully',
                'brand'   => [
                    'id'   => $brand->id,
                    'name' => $brand->name
                ],
                'data'    => $products
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function getSimilarProducts($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product not found',
                    'data' => []
                ], 200);
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


                    $item->final_price = $item->final_price;

                    $item->discount_percentage = ($item->mrp > 0 && $item->final_price < $item->mrp)
                        ? round((($item->mrp - $item->final_price) / $item->mrp) * 100)
                        : 0;

                    $item->discount_label = $item->discount_percentage > 0
                        ? $item->discount_percentage . '% OFF'
                        : null;

                    $item->image_urls = collect($images)->map(function ($img) {
                        return asset('storage/products/' . $img);
                    });

                    unset($item->product_images);

                    return $item;
                });

            return response()->json([
                'status'  => true,
                'message' => $similarProducts->isEmpty()
                    ? 'No similar products found'
                    : 'Similar products fetched successfully',
                'product_id' => $id,
                'data'    => $similarProducts
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function getProductDetails($id)
    {
        try {
            $product = Product::with([
                'category:id,name',
                'subCategory:id,name,category_id',
                'brand:id,name'
            ])->find($id);

            if (!$product) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Product not found',
                    'data'    => null
                ], 200);
            }

            $images = is_string($product->product_images)
                ? json_decode($product->product_images, true)
                : $product->product_images;

            $imageUrls = collect($images)->map(function ($img) {
                return asset('storage/products/' . $img);
            });

            return response()->json([
                'status'  => true,
                'message' => 'Product fetched successfully',
                'data'    => [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'description' => $product->description,
                    'sku'         => $product->sku,

                    'pricing' => [
                        'base_price'     => $product->base_price,
                        'retailer_price' => $product->retailer_price,
                        'mrp'            => $product->mrp,
                        'gst_percentage' => $product->gst_percentage,
                    ],

                    'stock'        => $product->stock,
                    'expiry_date'  => $product->expiry_date,
                    'images'       => $imageUrls,

                    'category'     => $product->category,
                    'sub_category' => $product->subCategory,
                    'brand'        => $product->brand,
                ]
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
    public function productsByBrand($brand_id)
    {
        $brand = Brand::find($brand_id);

        if (!$brand) {
            return response()->json([
                'status'  => false,
                'message' => 'Brand not found',
                'data'    => []
            ], 404);
        }

        $products = Product::where('brand_id', $brand_id)
            ->select(
                'id',
                'brand_id',
                'category_id',
                'name',
                'base_price',
                'retailer_price',
                'mrp',
                'gst_percentage',
                'product_images'
            )
            ->get()
            ->map(function ($product) {

                /* 🔹 STOCK SET (FROM WAREHOUSE) */
                $stock = (int) WarehouseStock::where('product_id', $product->id)
                    ->sum('quantity');

                /* 🔹 MAX STOCK SET (APP LIMIT) */
                $maxStockLimit = 10; // change if needed
                $product->stock = $stock;
                $product->max_stock = min($stock, $maxStockLimit);

                /* 🔹 IMAGE URL SET */
                $images = is_string($product->product_images)
                    ? json_decode($product->product_images, true)
                    : $product->product_images;

                $product->image_urls = collect($images)->map(function ($img) {
                    return asset('storage/products/' . $img);
                })->values();

                unset($product->product_images);

                return $product;
            });

        return response()->json([
            'status'  => true,
            'message' => $products->isEmpty()
                ? 'No products found'
                : 'Products fetched successfully',
            'brand' => [
                'id'   => $brand->id,
                'name' => $brand->name
            ],
            'data' => $products
        ], 200);
    }
}
