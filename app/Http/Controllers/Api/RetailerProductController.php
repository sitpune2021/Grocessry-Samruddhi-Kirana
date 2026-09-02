<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Retailer;
use App\Models\Product;
use App\Models\WarehouseTransfer;
use App\Models\RetailerPricing;
use Illuminate\Support\Facades\Log;
use App\Models\Warehouse;


class RetailerProductController extends Controller
{


    /**
     * Retailer Product List
     *
     * Features:
     * - Pagination
     * - Search
     * - Category filter
     * - Retailer-specific pricing
     * - Only retailer's Distribution Center products
     */
    public function index(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            Log::info('Retailer Product List - User', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 2. Get Retailer
            |--------------------------------------------------------------------------
            |
            | users.id = retailers.user_id
            |
            */

            $retailer = Retailer::where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();


            if (!$retailer) {

                Log::warning(
                    'Retailer Product List - Retailer Not Found',
                    [
                        'user_id' => $user->id,
                    ]
                );

                return response()->json([
                    'status'  => false,
                    'message' => 'Retailer account not found or inactive.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 3. Get Distribution Center
            |--------------------------------------------------------------------------
            |
            | retailers.shop_id = warehouse.id
            |
            */

            $warehouse = Warehouse::where('id', $retailer->shop_id)
                ->where('type', 'distribution_center')
                ->where('status', 'active')
                ->first();


            if (!$warehouse) {

                Log::warning(
                    'Retailer Product List - DC Not Found',
                    [
                        'retailer_id' => $retailer->id,
                        'shop_id'     => $retailer->shop_id,
                    ]
                );

                return response()->json([
                    'status'  => false,
                    'message' => 'Distribution Center not found or inactive.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Pagination
            |--------------------------------------------------------------------------
            */

            $perPage = (int) $request->get('per_page', 10);

            if ($perPage < 1) {
                $perPage = 10;
            }

            if ($perPage > 100) {
                $perPage = 100;
            }


            /*
            |--------------------------------------------------------------------------
            | 5. Search
            |--------------------------------------------------------------------------
            */

            $search = trim(
                $request->get('search', '')
            );


            /*
            |--------------------------------------------------------------------------
            | 6. Category Filter
            |--------------------------------------------------------------------------
            */

            $categoryId = $request->get('category_id');


            /*
            |--------------------------------------------------------------------------
            | 7. Retailer Pricing Query
            |--------------------------------------------------------------------------
            |
            | Products are taken from retailer_pricings.
            |
            | This means retailer will see only products
            | for which pricing has been assigned.
            |
            */

            $query = RetailerPricing::with([
                'product:id,name,base_price,retailer_price,mrp,gst_percentage,final_price,stock,product_images',
                'category:id,name',
                'warehouse:id,name,type',
            ])

            /*
            |--------------------------------------------------------------------------
            | Retailer
            |--------------------------------------------------------------------------
            */

            ->where('retailer_id', $retailer->id)

            /*
            |--------------------------------------------------------------------------
            | Distribution Center
            |--------------------------------------------------------------------------
            */

            ->where('warehouse_id', $warehouse->id)

            /*
            |--------------------------------------------------------------------------
            | Active Pricing
            |--------------------------------------------------------------------------
            */

            ->where('is_active', 1);


            /*
            |--------------------------------------------------------------------------
            | 8. Effective Date
            |--------------------------------------------------------------------------
            |
            | Only currently applicable pricing.
            |
            */

            $query->whereDate(
                'effective_from',
                '<=',
                now()->toDateString()
            );

            $query->where(function ($q) {

                $q->whereNull('effective_to')
                    ->orWhereDate(
                        'effective_to',
                        '>=',
                        now()->toDateString()
                    );

            });


            /*
            |--------------------------------------------------------------------------
            | 9. Search Product
            |--------------------------------------------------------------------------
            */

            if ($search !== '') {

                $query->whereHas(
                    'product',
                    function ($q) use ($search) {

                        $q->where(
                            'name',
                            'LIKE',
                            '%' . $search . '%'
                        );

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | 10. Category Filter
            |--------------------------------------------------------------------------
            */

            // if (!empty($categoryId)) {

            //     $query->where(
            //         'category_id',
            //         $categoryId
            //     );

            // }

            /*
                |--------------------------------------------------------------------------
                | 10. Category Filter
                |--------------------------------------------------------------------------
                |
                | category_id = all / null / empty => show all categories
                | specific category_id => filter by that category
                |
            */

            if (
                !empty($categoryId) &&
                strtolower((string) $categoryId) !== 'all'
            ) {
                $query->where('category_id', $categoryId);
            }

            /*
            |--------------------------------------------------------------------------
            | 11. Latest Products First
            |--------------------------------------------------------------------------
            */

            $query->latest('id');


            /*
            |--------------------------------------------------------------------------
            | 12. Pagination
            |--------------------------------------------------------------------------
            */

            $pricings = $query->paginate($perPage);


            /*
            |--------------------------------------------------------------------------
            | 13. Format Products
            |--------------------------------------------------------------------------
            */

            $products = $pricings->getCollection()
                ->map(function ($pricing) {

                    $product = $pricing->product;

                    return [

                        'pricing_id' =>
                            $pricing->id,

                        'product_id' =>
                            $pricing->product_id,

                        'product_name' =>
                            $product?->name,

                        'category' => [

                            'id' =>
                                $pricing->category_id,

                            'name' =>
                                $pricing->category?->name,

                        ],

                        /*
                        |--------------------------------------------------------------------------
                        | Product Pricing
                        |--------------------------------------------------------------------------
                        */

                        'mrp' =>
                            $product?->mrp,

                        'base_price' =>
                            $pricing->base_price,

                        'discount_percent' =>
                            $pricing->discount_percent,

                        'discount_amount' =>
                            $pricing->discount_amount,

                        'effective_price' =>
                            $pricing->effective_price,

                        /*
                        |--------------------------------------------------------------------------
                        | Product Information
                        |--------------------------------------------------------------------------
                        */

                        'gst_percentage' =>
                            $product?->gst_percentage,

                        'final_price' =>
                            $product?->final_price,

                        'stock' =>
                            $product?->stock,

                        'product_images' => collect($product?->product_images ?? [])
                        ->map(function ($image) {
                            return asset('storage/products/' . $image);
                        })
                        ->values()
                        ->toArray(),


                        /*
                        |--------------------------------------------------------------------------
                        | Pricing Dates
                        |--------------------------------------------------------------------------
                        */

                        'effective_from' =>
                            $pricing->effective_from,

                        'effective_to' =>
                            $pricing->effective_to,

                        'is_active' =>
                            (bool) $pricing->is_active,

                    ];

                })
                ->values();


            /*
            |--------------------------------------------------------------------------
            | 14. Success Log
            |--------------------------------------------------------------------------
            */

            Log::info(
                'Retailer Product List - Success',
                [
                    'retailer_id' => $retailer->id,
                    'warehouse_id' => $warehouse->id,
                    'search' => $search,
                    'category_id' => $categoryId,
                    'per_page' => $perPage,
                    'total_products' => $pricings->total(),
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | 15. Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'status' => true,

                'message' =>
                    'Retailer products fetched successfully.',

                'retailer' => [

                    'id' =>
                        $retailer->id,

                    'name' =>
                        $retailer->name,

                    'shop_name' =>
                        $retailer->shop_name,

                ],

                'distribution_center' => [

                    'id' =>
                        $warehouse->id,

                    'name' =>
                        $warehouse->name,

                ],

                'filters' => [

                    'search' =>
                        $search,

                    'category_id' =>
                        $categoryId,

                    'per_page' =>
                        $perPage,

                ],

                'products' =>
                    $products,

                'pagination' => [

                    'current_page' =>
                        $pricings->currentPage(),

                    'last_page' =>
                        $pricings->lastPage(),

                    'per_page' =>
                        $pricings->perPage(),

                    'total' =>
                        $pricings->total(),

                    'from' =>
                        $pricings->firstItem(),

                    'to' =>
                        $pricings->lastItem(),

                ],

            ], 200);


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Error Log
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Retailer Product List - Exception',
                [
                    'user_id' =>
                        $request->user()?->id,

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),

                ]
            );


            return response()->json([
                'status'  => false,
                'message' => 'Unable to fetch retailer products.',
                'error'   => $e->getMessage(), 
                'file'    => $e->getFile(),     
                'line'    => $e->getLine(), 
            ], 500);
        }
    }


    /**
     * =========================================================
     * Retailer Product Details
     * =========================================================
     *
     * GET /api/retailer/products/{product}
     */
    public function show(Request $request, $productId)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            Log::info('Retailer Product Details - Request', [
                'user_id' => $user?->id,
                'product_id' => $productId,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 2. Verify Retailer
            |--------------------------------------------------------------------------
            */

            $retailer = Retailer::where(
                'user_id',
                $user->id
            )
            ->where('is_active', 1)
            ->first();


            if (!$retailer) {

                Log::warning(
                    'Retailer Product Details - Retailer Not Found',
                    [
                        'user_id' => $user->id,
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Retailer profile not found.'
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 3. Get Retailer's Distribution Center
            |--------------------------------------------------------------------------
            |
            | retailers.shop_id = warehouse.id
            |
            */

            $warehouseId = $retailer->shop_id;


            /*
            |--------------------------------------------------------------------------
            | 4. Get Product
            |--------------------------------------------------------------------------
            */

            $product = Product::with([
                'category:id,name',
                'brand:id,name',
                'unit:id,name',
            ])
            ->where('id', $productId)
            ->first();


            if (!$product) {

                Log::warning(
                    'Retailer Product Details - Product Not Found',
                    [
                        'product_id' => $productId,
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 5. Verify Product Available In Retailer's DC
            |--------------------------------------------------------------------------
            */

            $productAvailable = WarehouseTransfer::where(
                'requested_by_warehouse_id',
                $warehouseId
            )
            ->where(
                'product_id',
                $productId
            )
            ->exists();


            if (!$productAvailable) {

                Log::warning(
                    'Retailer Product Details - Product Not Available In DC',
                    [
                        'retailer_id' => $retailer->id,
                        'warehouse_id' => $warehouseId,
                        'product_id' => $productId,
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' =>
                        'This product is not available in your Distribution Center.'
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 6. Get Retailer Pricing
            |--------------------------------------------------------------------------
            */

            $pricing = RetailerPricing::where(
                'retailer_id',
                $retailer->id
            )
            ->where(
                'warehouse_id',
                $warehouseId
            )
            ->where(
                'product_id',
                $productId
            )
            ->where('is_active', 1)
            ->latest('effective_from')
            ->first();


            /*
            |--------------------------------------------------------------------------
            | 7. Product Details Response
            |--------------------------------------------------------------------------
            */

            $response = [

                'id' => $product->id,

                'name' => $product->name,

                'category' => $product->category
                    ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                    ]
                    : null,

                'brand' => $product->brand
                    ? [
                        'id' => $product->brand->id,
                        'name' => $product->brand->name,
                    ]
                    : null,

                'unit' => $product->unit
                    ? [
                        'id' => $product->unit->id,
                        'name' => $product->unit->name,
                    ]
                    : null,

                'unit_value' => $product->unit_value,

                'sku' => $product->sku,

                'barcode' => $product->barcode,

                'description' => $product->description,

                /*
                |--------------------------------------------------------------------------
                | Product Original Pricing
                |--------------------------------------------------------------------------
                */

                'mrp' => $product->mrp,

                'base_price' => $product->base_price,

                /*
                |--------------------------------------------------------------------------
                | Retailer Pricing
                |--------------------------------------------------------------------------
                */

                'retailer_pricing' => $pricing
                    ? [
                        'pricing_id' => $pricing->id,

                        'base_price' =>
                            $pricing->base_price,

                        'discount_percent' =>
                            $pricing->discount_percent,

                        'discount_amount' =>
                            $pricing->discount_amount,

                        'effective_price' =>
                            $pricing->effective_price,

                        'effective_from' =>
                            $pricing->effective_from,

                        'effective_to' =>
                            $pricing->effective_to,

                        'is_active' =>
                            (bool) $pricing->is_active,
                    ]
                    : null,

                /*
                |--------------------------------------------------------------------------
                | Stock
                |--------------------------------------------------------------------------
                */

                'stock' => $product->stock,

                /*
                |--------------------------------------------------------------------------
                | Product Images
                |--------------------------------------------------------------------------
                */

                //'product_images' => $product->product_images,
                'product_images' => collect($product?->product_images ?? [])
                ->map(function ($image) {
                    return asset('storage/products/' . $image);
                })
                ->values()
                ->toArray(),
            ];


            /*
            |--------------------------------------------------------------------------
            | 8. Success Log
            |--------------------------------------------------------------------------
            */

            Log::info(
                'Retailer Product Details - Success',
                [
                    'retailer_id' => $retailer->id,
                    'warehouse_id' => $warehouseId,
                    'product_id' => $product->id,
                    'pricing_id' => $pricing?->id,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | 9. Response
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,
                'message' => 'Product details fetched successfully.',
                'data' => $response
            ], 200);


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Error Log
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Retailer Product Details - Exception',
                [
                    'user_id' => $request->user()?->id,
                    'product_id' => $productId,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );


            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching product details.',
            ], 500);
        }
    }


    /**
     * Retailer Category List
     *
     * Returns only categories for which:
     * - Retailer is active
     * - Retailer belongs to the logged-in user
     * - Retailer belongs to an active Distribution Center
     * - Retailer has active pricing
     * - Pricing is currently effective
    */
    public function categories(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | 1. Logged-in User
            |--------------------------------------------------------------------------
            */

            $user = $request->user();

            Log::info('Retailer Category List - User', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);


            /*
            |--------------------------------------------------------------------------
            | 2. Get Retailer
            |--------------------------------------------------------------------------
            */

            $retailer = Retailer::where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();


            if (!$retailer) {

                Log::warning(
                    'Retailer Category List - Retailer Not Found',
                    [
                        'user_id' => $user->id,
                    ]
                );

                return response()->json([
                    'status'  => false,
                    'message' => 'Retailer account not found or inactive.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 3. Get Distribution Center
            |--------------------------------------------------------------------------
            |
            | retailers.shop_id = warehouse.id
            |
            */

            $warehouse = Warehouse::where(
                    'id',
                    $retailer->shop_id
                )
                ->where(
                    'type',
                    'distribution_center'
                )
                ->where(
                    'status',
                    'active'
                )
                ->first();


            if (!$warehouse) {

                Log::warning(
                    'Retailer Category List - DC Not Found',
                    [
                        'retailer_id' => $retailer->id,
                        'shop_id'     => $retailer->shop_id,
                    ]
                );

                return response()->json([
                    'status'  => false,
                    'message' => 'Distribution Center not found or inactive.',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Get Categories
            |--------------------------------------------------------------------------
            |
            | Only categories having active/current retailer pricing.
            |
            */

            $categories = RetailerPricing::query()

                /*
                |--------------------------------------------------------------------------
                | Retailer
                |--------------------------------------------------------------------------
                */

                ->where(
                    'retailer_id',
                    $retailer->id
                )

                /*
                |--------------------------------------------------------------------------
                | Distribution Center
                |--------------------------------------------------------------------------
                */

                ->where(
                    'warehouse_id',
                    $warehouse->id
                )

                /*
                |--------------------------------------------------------------------------
                | Active Pricing
                |--------------------------------------------------------------------------
                */

                ->where(
                    'is_active',
                    1
                )

                /*
                |--------------------------------------------------------------------------
                | Effective From
                |--------------------------------------------------------------------------
                */

                ->whereDate(
                    'effective_from',
                    '<=',
                    now()->toDateString()
                )

                /*
                |--------------------------------------------------------------------------
                | Effective To
                |--------------------------------------------------------------------------
                */

                ->where(function ($query) {

                    $query->whereNull('effective_to')
                        ->orWhereDate(
                            'effective_to',
                            '>=',
                            now()->toDateString()
                        );

                })

                /*
                |--------------------------------------------------------------------------
                | Category Relationship
                |--------------------------------------------------------------------------
                */

                ->with('category:id,name,category_images')


                /*
                |--------------------------------------------------------------------------
                | Unique Categories
                |--------------------------------------------------------------------------
                */

                ->get()

                ->pluck('category')

                ->filter()

                ->unique('id')

                ->sortBy('name')

                ->values();


            /*
            |--------------------------------------------------------------------------
            | 5. Log
            |--------------------------------------------------------------------------
            */

            Log::info(
                'Retailer Category List - Success',
                [
                    'retailer_id' =>
                        $retailer->id,

                    'warehouse_id' =>
                        $warehouse->id,

                    'categories_count' =>
                        $categories->count(),
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | 6. Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'status' => true,

                'message' =>
                    'Retailer categories fetched successfully.',

                'retailer' => [

                    'id' =>
                        $retailer->id,

                    'name' =>
                        $retailer->name,

                    'shop_name' =>
                        $retailer->shop_name,

                ],

                'distribution_center' => [

                    'id' =>
                        $warehouse->id,

                    'name' =>
                        $warehouse->name,

                ],

                // 'categories' =>
                //     $categories->map(function ($category) {

                //         return [

                //             'id' =>
                //                 $category->id,

                //             'name' =>
                //                 $category->name,

                //         ];

                //     })->values(),

                'categories' =>
                    $categories->map(function ($category) {

                        return [

                            'id' => $category->id,

                            'name' => $category->name,

                            'category_images' => collect(
                                $category->category_images ?? []
                            )->map(function ($image) {

                                return asset('storage/categories/' . $image);

                            })->values()->toArray(),

                        ];

                    })->values(),

                'total' =>
                    $categories->count(),

            ], 200);


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Error Log
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Retailer Category List - Exception',
                [
                    'user_id' =>
                        $request->user()?->id,

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );


            return response()->json([

                'status' =>
                    false,

                'message' =>
                    'Unable to fetch retailer categories.',

            ], 500);
        }
    }


}