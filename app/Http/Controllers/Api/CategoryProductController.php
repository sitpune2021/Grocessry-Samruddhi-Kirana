<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\WarehouseStock;
use App\Models\Banner;
use App\Models\WarehouseServicePincode;
use Illuminate\Support\Facades\Session;

class CategoryProductController extends Controller
{
    public function getCategories()
    {
        try {
            $categories = Category::select(
                'id',
                'name',
                'slug',
                'category_images'
            )
                ->orderBy('id')
                ->get();

            $data = [];

            foreach ($categories as $category) {

                $images = [];

                if (is_array($category->category_images)) {
                    foreach ($category->category_images as $img) {
                        $images[] = asset('storage/categories/' . $img);
                    }
                }

                $data[] = [
                    'id'     => $category->id,
                    'name'   => $category->name,
                    'slug'   => $category->slug,
                    'images' => $images
                ];
            }

            return response()->json([
                'status'  => true,
                'message' => 'Categories fetched successfully',
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSubCategoriesByCategory($id)
    {
        try {
            $subcategories = SubCategory::where('category_id', $id)
                ->select('id', 'name')
                ->withCount('products')
                ->orderBy('id')
                ->get();

            return response()->json([
                'status'      => true,
                'message'     => $subcategories->isEmpty()
                    ? 'No subcategories found'
                    : 'Subcategories fetched successfully',
                'category_id' => (int) $id,
                'data'        => $subcategories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getBrands()
    {
        try {
            $brands = Brand::select('id', 'name', 'logo')
                ->orderBy('id')
                ->get();

            $data = [];

            foreach ($brands as $brand) {
                $data[] = [
                    'id'    => $brand->id,
                    'name'  => $brand->name,
                    'image' => $brand->logo
                        ? asset('storage/brands/' . $brand->logo)
                        : null
                ];
            }

            return response()->json([
                'status'  => true,
                'message' => $brands->isEmpty()
                    ? 'No brands found'
                    : 'Brands fetched successfully',
                'data'    => $data   //  RETURN THIS
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function getProductsBySubcategory($id, Request $request)
    {
        $warehouseId = $request->header('warehouse-id');

        if (!$warehouseId) {
            return response()->json([
                'status' => false,
                'message' => 'Warehouse not selected'
            ], 400);
        }

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
            ->map(function ($product) use ($warehouseId) {

                // WAREHOUSE STOCK ONLY
                $availableStock = WarehouseStock::where('product_id', $product->id)
                    ->where('warehouse_id', $warehouseId)
                    ->sum('quantity');

                // Discount
                $discountPercent = 0;
                if ($product->mrp > 0 && $product->final_price < $product->mrp) {
                    $discountPercent = round(
                        (($product->mrp - $product->final_price) / $product->mrp) * 100
                    );
                }

                // Images
                $images = is_string($product->product_images)
                    ? json_decode($product->product_images, true)
                    : ($product->product_images ?? []);

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

                    'stock' => (int) $availableStock,
                    'quantity' => 1,
                    'max_quantity' => (int) $availableStock,
                    'in_stock' => $availableStock > 0,

                    'image_urls' => collect($images)->map(
                        fn($img) => asset('storage/products/' . $img)
                    )->values()
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
    public function getProductsByBrand($id, Request $request)
    {
        try {

            $warehouseId = $request->header('warehouse-id');

            if (!$warehouseId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Warehouse not selected'
                ], 400);
            }

            $brand = Brand::find($id);

            if (!$brand) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Brand not found',
                    'data'    => []
                ], 200);
            }

            $products = Product::select(
                'products.id',
                'products.brand_id',
                'products.category_id',
                'products.name',
                'products.base_price',
                'products.retailer_price',
                'products.mrp',
                'products.gst_percentage',
                'products.final_price',
                'products.product_images'
            )

                // STOCK SUBQUERY (NO GROUP BY ISSUE)
                ->selectSub(function ($query) use ($warehouseId) {
                    $query->from('warehouse_stock')
                        ->selectRaw('COALESCE(SUM(quantity),0)')
                        ->whereColumn('warehouse_stock.product_id', 'products.id')
                        ->where('warehouse_stock.warehouse_id', $warehouseId);
                }, 'stock')

                ->where('products.brand_id', $id)
                ->get()

                ->map(function ($product) {

                    // MAX STOCK
                    $maxStockLimit = 10;
                    $product->max_stock = min($product->stock, $maxStockLimit);

                    // DISCOUNT
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

                    // STOCK FLAGS
                    $product->stock = (int) $product->stock;
                    $product->in_stock = $product->stock > 0;

                    // IMAGES
                    $images = is_string($product->product_images)
                        ? json_decode($product->product_images, true)
                        : ($product->product_images ?? []);

                    $product->image_urls = collect($images)->map(
                        fn($img) => asset('storage/products/' . $img)
                    )->values();

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
                'message' => 'Internal server error',
                'error'   => $e->getMessage() // remove in production
            ], 500);
        }
    }
    public function getSimilarProducts($id, Request $request)
    {
        try {

            $warehouseId = $request->header('warehouse-id');

            if (!$warehouseId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Warehouse not selected'
                ], 400);
            }

            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Product not found',
                    'data'    => []
                ], 200);
            }

            $similarProducts = Product::select('products.*')
                ->selectSub(function ($query) use ($warehouseId) {
                    $query->from('warehouse_stock')
                        ->selectRaw('COALESCE(SUM(quantity),0)')
                        ->whereColumn('warehouse_stock.product_id', 'products.id')
                        ->where('warehouse_stock.warehouse_id', $warehouseId);
                }, 'stock')

                ->where('products.id', '!=', $product->id)

                ->where(function ($query) use ($product) {
                    $query->where('products.sub_category_id', $product->sub_category_id)
                        ->orWhere('products.category_id', $product->category_id);
                })

                ->orderByRaw('stock > 0 DESC')

                ->orderByRaw(
                    "CASE
            WHEN products.sub_category_id = ? THEN 1
            WHEN products.category_id = ? THEN 2
            ELSE 3
        END",
                    [$product->sub_category_id, $product->category_id]
                )

                ->limit(12)
                ->get()

                ->map(function ($item) {

                    $images = is_string($item->product_images)
                        ? json_decode($item->product_images, true) ?? []
                        : ($item->product_images ?? []);

                    $discountPercentage = ($item->mrp > 0 && $item->final_price < $item->mrp)
                        ? round((($item->mrp - $item->final_price) / $item->mrp) * 100)
                        : 0;

                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'category_id' => $item->category_id,
                        'sub_category_id' => $item->sub_category_id,

                        'mrp' => (float) $item->mrp,
                        'final_price' => (float) $item->final_price,

                        'discount_percentage' => $discountPercentage,
                        'discount_label' => $discountPercentage > 0
                            ? $discountPercentage . '% OFF'
                            : null,

                        'stock' => (int) $item->stock,
                        'in_stock' => $item->stock > 0,

                        'image_urls' => collect($images)->map(
                            fn($img) => asset('storage/products/' . $img)
                        )->values(),
                    ];
                });

            return response()->json([
                'status'     => true,
                'message'    => $similarProducts->isEmpty()
                    ? 'No similar products found'
                    : 'Similar products fetched successfully',
                'product_id' => $id,
                'data'       => $similarProducts
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Internal server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getProductDetails($id, Request $request)
    {


        try {
            $product = Product::with([
                'category:id,name',
                'subCategory:id,name',
                'brand:id,name'
            ])->find($id);

            if (!$product) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product not found',
                    'data' => null
                ], 200);
            }

            //  GET warehouse from session
            $warehouseId = $request->header('warehouse_id');
            if (!$warehouseId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pincode not selected'
                ], 400);
            }

            //  STOCK ONLY FROM SELECTED WAREHOUSE
            $availableStock = (int) WarehouseStock::where('product_id', $product->id)
                ->where('warehouse_id', $warehouseId)
                ->whereNull('deleted_at')
                ->sum('quantity');

            //  Discount
            $discountPercent = 0;
            if ($product->mrp > 0 && $product->final_price < $product->mrp) {
                $discountPercent = round(
                    (($product->mrp - $product->final_price) / $product->mrp) * 100
                );
            }

            // Images
            $images = is_string($product->product_images)
                ? json_decode($product->product_images, true)
                : ($product->product_images ?? []);

            return response()->json([
                'status' => true,
                'message' => 'Product fetched successfully',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'sku' => $product->sku,

                    'pricing' => [
                        'base_price' => (float) $product->base_price,
                        'retailer_price' => (float) $product->retailer_price,
                        'final_price' => (float) $product->final_price,
                        'mrp' => (float) $product->mrp,
                        'gst_percentage' => (float) $product->gst_percentage,
                    ],

                    'discount_percentage' => $discountPercent,
                    'discount_label' => $discountPercent > 0
                        ? $discountPercent . '% OFF'
                        : null,

                    'stock' => $availableStock,
                    'max_quantity' => $availableStock,
                    'quantity' => 1,

                    'in_stock' => $availableStock > 0, // useful for frontend

                    'expiry_date' => optional($product->expiry_date)->format('Y-m-d'),

                    'images' => collect($images)->map(
                        fn($img) => asset('storage/products/' . $img)
                    )->values(),

                    'category' => $product->category,
                    'sub_category' => $product->subCategory,
                    'brand' => $product->brand,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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

                /*  STOCK SET (FROM WAREHOUSE) */
                $stock = (int) WarehouseStock::where('product_id', $product->id)
                    ->sum('quantity');

                /*  MAX STOCK SET (APP LIMIT) */
                $maxStockLimit = 10; // change if needed
                $product->stock = $stock;
                $product->max_stock = min($stock, $maxStockLimit);

                /*  IMAGE URL SET */
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
    public function getBanners()
    {
        try {
            $banners = Banner::orderBy('id')->get();

            $data = $banners->map(function ($banner) {
                return [
                    'id'         => $banner->id,
                    'name'       => $banner->name,
                    'product_id' => $banner->product_id,
                    'image'      => $banner->image
                        ? asset('storage/' . $banner->image)
                        : null
                ];
            });


            return response()->json([
                'status'  => true,
                'message' => $data->isEmpty()
                    ? 'No banners found'
                    : 'Banners fetched successfully',
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
