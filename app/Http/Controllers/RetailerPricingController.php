<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Retailer;
use App\Models\RetailerPricing;
use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;


class RetailerPricingController extends Controller
{


    /**
     * Pricing List
     */
    public function index()
    {
        $pricings = RetailerPricing::with([
            'retailer:id,name',
            'warehouse:id,name,type',
            'category:id,name',
            'product:id,name'
        ])
        ->latest()
        ->paginate(10);

        return view(
            'retailer-pricing.index',
            compact('pricings')
        );
    }


    /**
     * Create Pricing Form
     */
    public function create()
    {
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | 1. Logged-in user's Distribution Center
    |--------------------------------------------------------------------------
    */

    $warehouse = Warehouse::where('id', $user->warehouse_id)
        ->where('type', 'distribution_center')
        ->where('status', 'active')
        ->firstOrFail();


    /*
    |--------------------------------------------------------------------------
    | 2. Only logged-in user's DC
    |--------------------------------------------------------------------------
    */

    $warehouses = collect([$warehouse]);


    /*
    |--------------------------------------------------------------------------
    | 3. Get Retailers According To DC
    |--------------------------------------------------------------------------
    |
    | Current database structure:
    |
    | warehouse.id = retailers.shop_id
    |
    */

    $retailers = Retailer::where('is_active', 1)
        ->where('shop_id', $warehouse->id)
        ->orderBy('name', 'asc')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | 4. Get Products Available In This DC
    |--------------------------------------------------------------------------
    */

    $productIds = WarehouseTransfer::where(
            'requested_by_warehouse_id',
            $warehouse->id
        )
        ->whereNotNull('product_id')
        ->pluck('product_id')
        ->unique();


    /*
    |--------------------------------------------------------------------------
    | 5. Get Categories Available In This DC
    |--------------------------------------------------------------------------
    */

    $categories = Category::whereHas('products', function ($query) use ($productIds) {

        $query->whereIn('id', $productIds);

    })
    ->orderBy('name', 'asc')
    ->get();


    /*
    |--------------------------------------------------------------------------
    | 6. Return Pricing Form
    |--------------------------------------------------------------------------
    */

    return view(
        'retailer-pricing.form',
        compact(
            'retailers',
            'warehouses',
            'categories',
            'warehouse'
        )
    );
    }


    /**
     * Get Products Available In Selected DC
     */
    public function getProductsByWarehouse($warehouseId)
    {
        $warehouse = Warehouse::where('id', $warehouseId)
            ->where('type', 'distribution_center')
            ->where('status', 'active')
            ->firstOrFail();


        $products = WarehouseTransfer::where(
                'requested_by_warehouse_id',
                $warehouse->id
            )
            ->whereNotNull('product_id')
            ->with('product:id,name,category_id')
            ->select('product_id')
            ->distinct()
            ->get()
            ->filter(function ($transfer) {

                return $transfer->product !== null;

            })
            ->map(function ($transfer) {

                return [
                    'id' => $transfer->product->id,
                    'name' => $transfer->product->name,
                    'category_id' => $transfer->product->category_id,
                ];

            })
            ->values();


        return response()->json($products);
    }


    /**
        * Get Products By DC + Category
    */
    public function getProductsByCategoryAndWarehouse(
        $warehouseId,
        $categoryId
    ) {
        $products = WarehouseTransfer::where(
                'requested_by_warehouse_id',
                $warehouseId
            )
            ->whereNotNull('product_id')
            ->whereHas('product', function ($query) use ($categoryId) {

                $query->where('category_id', $categoryId);

            })
            ->with('product:id,name,category_id,mrp')
            ->select('product_id')
            ->distinct()
            ->get()
            ->filter(function ($transfer) {

                return $transfer->product !== null;

            })
            ->map(function ($transfer) {

                return [
                    'id'   => $transfer->product->id,
                    'name' => $transfer->product->name,
                    'mrp'  => $transfer->product->mrp,
                ];

            })
            ->values();

        return response()->json($products);
    }
    

    /**
     * Store Pricing
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate Request
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([

            'retailer_id' => [
                'required',
                'exists:retailers,id'
            ],

            'warehouse_id' => [
                'required',
                'exists:warehouses,id'
            ],

            'category_id' => [
                'required',
                'exists:categories,id'
            ],

            'product_id' => [
                'required',
                'exists:products,id'
            ],

            'base_price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'discount_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],

            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'effective_from' => [
                'required',
                'date'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | LOG 1 - Incoming Request
        |--------------------------------------------------------------------------
        */

        \Log::info('Retailer Pricing Store - Request Data', [
            'data' => $data,
            'user_id' => auth()->id(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. Get Logged-in User
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();

        \Log::info('Retailer Pricing Store - Logged User', [
            'user_id' => $user->id,
            'user_warehouse_id' => $user->warehouse_id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | 3. Verify Distribution Center
        |--------------------------------------------------------------------------
        */

        $warehouse = Warehouse::where('id', $data['warehouse_id'])
            ->where('type', 'distribution_center')
            ->where('status', 'active')
            ->first();


        \Log::info('Retailer Pricing Store - Warehouse', [
            'warehouse_found' => $warehouse ? true : false,
            'warehouse_id' => $warehouse?->id,
            'warehouse_name' => $warehouse?->name,
            'warehouse_shop_id' => $warehouse?->shop_id,
        ]);


        if (!$warehouse) {

            \Log::warning(
                'Retailer Pricing Store - Invalid Distribution Center',
                [
                    'warehouse_id' => $data['warehouse_id']
                ]
            );

            return back()
                ->withErrors([
                    'warehouse_id' =>
                        'Selected warehouse is not a valid active Distribution Center.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Security Check
        |--------------------------------------------------------------------------
        |
        | User sirf apne assigned DC mein pricing create kar sakta hai.
        |
        */

        if ((int) $warehouse->id !== (int) $user->warehouse_id) {

            \Log::warning(
                'Retailer Pricing Store - Unauthorized DC',
                [
                    'user_warehouse_id' => $user->warehouse_id,
                    'selected_warehouse_id' => $warehouse->id,
                ]
            );

            return back()
                ->withErrors([
                    'warehouse_id' =>
                        'You are not authorized to use this Distribution Center.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Verify Retailer Belongs To This DC
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | retailers.shop_id = warehouse.id
        |
        | Example:
        | warehouse.id = 4
        | retailers.shop_id = 4
        |
        */

        $retailer = Retailer::where('id', $data['retailer_id'])
            ->where('is_active', 1)
            ->where('shop_id', $warehouse->id)
            ->first();


        /*
        |--------------------------------------------------------------------------
        | LOG 2 - Retailer Check
        |--------------------------------------------------------------------------
        */

        \Log::info('Retailer Pricing Store - Retailer Check', [
            'retailer_id' => $data['retailer_id'],
            'retailer_found' => $retailer ? true : false,
            'retailer_name' => $retailer?->name,
            'retailer_shop_id' => $retailer?->shop_id,
            'warehouse_id' => $warehouse->id,
            'warehouse_shop_id' => $warehouse->shop_id,
        ]);


        if (!$retailer) {

            /*
            |----------------------------------------------------------------------
            | Extra Debug Log
            |----------------------------------------------------------------------
            */

            $debugRetailer = Retailer::find($data['retailer_id']);

            \Log::warning(
                'Retailer Pricing Store - Retailer Does Not Belong To DC',
                [
                    'retailer_id' => $data['retailer_id'],

                    'retailer_exists' => $debugRetailer ? true : false,

                    'retailer_shop_id' =>
                        $debugRetailer?->shop_id,

                    'warehouse_id' =>
                        $warehouse->id,

                    'warehouse_shop_id' =>
                        $warehouse->shop_id,

                    'expected_shop_id' =>
                        $warehouse->id,
                ]
            );


            return back()
                ->withErrors([
                    'retailer_id' =>
                        'Selected retailer does not belong to this Distribution Center.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 6. Get Product
        |--------------------------------------------------------------------------
        */

        $product = Product::find($data['product_id']);


        \Log::info('Retailer Pricing Store - Product Check', [
            'product_id' => $data['product_id'],
            'product_found' => $product ? true : false,
            'product_name' => $product?->name,
            'product_category_id' => $product?->category_id,
            'selected_category_id' => $data['category_id'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 7. Verify Product Category
        |--------------------------------------------------------------------------
        */

        if (!$product || (int) $product->category_id !== (int) $data['category_id']) {

            \Log::warning(
                'Retailer Pricing Store - Product Category Mismatch',
                [
                    'product_id' => $data['product_id'],
                    'product_category_id' => $product?->category_id,
                    'selected_category_id' => $data['category_id'],
                ]
            );

            return back()
                ->withErrors([
                    'product_id' =>
                        'Selected product does not belong to selected category.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 8. Verify Product Available In DC
        |--------------------------------------------------------------------------
        */

        $productExists = WarehouseTransfer::where(
                'requested_by_warehouse_id',
                $warehouse->id
            )
            ->where(
                'product_id',
                $data['product_id']
            )
            ->exists();


        \Log::info('Retailer Pricing Store - Product DC Check', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $data['product_id'],
            'product_available' => $productExists,
        ]);


        if (!$productExists) {

            \Log::warning(
                'Retailer Pricing Store - Product Not Available In DC',
                [
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $data['product_id'],
                ]
            );

            return back()
                ->withErrors([
                    'product_id' =>
                        'Selected product is not available in this Distribution Center.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 9. Duplicate Pricing Check
        |--------------------------------------------------------------------------
        */

        $exists = RetailerPricing::where(
            'retailer_id',
            $data['retailer_id']
        )
        ->where(
            'warehouse_id',
            $data['warehouse_id']
        )
        ->where(
            'category_id',
            $data['category_id']
        )
        ->where(
            'product_id',
            $data['product_id']
        )
        ->whereDate(
            'effective_from',
            $data['effective_from']
        )
        ->exists();


        \Log::info('Retailer Pricing Store - Duplicate Check', [
            'duplicate_found' => $exists,
            'retailer_id' => $data['retailer_id'],
            'warehouse_id' => $data['warehouse_id'],
            'category_id' => $data['category_id'],
            'product_id' => $data['product_id'],
            'effective_from' => $data['effective_from'],
        ]);


        if ($exists) {

            return back()
                ->withErrors([
                    'product_id' =>
                        'Pricing already exists for this retailer, DC, category and product.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 10. Calculate Discount
        |--------------------------------------------------------------------------
        */

        $discountPercent =
            $data['discount_percent'] ?? 0;

        $discountAmount =
            $data['discount_amount'] ?? 0;


        /*
        |--------------------------------------------------------------------------
        | If Discount % is given but Amount is empty
        |--------------------------------------------------------------------------
        */

        if (
            empty($discountAmount) &&
            !empty($discountPercent)
        ) {

            $discountAmount =
                ($data['base_price'] * $discountPercent) / 100;
        }


        /*
        |--------------------------------------------------------------------------
        | 11. Calculate Effective Price
        |--------------------------------------------------------------------------
        */

        $effectivePrice =
            $data['base_price'] - $discountAmount;


        \Log::info('Retailer Pricing Store - Price Calculation', [
            'base_price' => $data['base_price'],
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'effective_price' => $effectivePrice,
        ]);


        if ($effectivePrice < 0) {

            \Log::warning(
                'Retailer Pricing Store - Negative Effective Price',
                [
                    'base_price' => $data['base_price'],
                    'discount_amount' => $discountAmount,
                ]
            );

            return back()
                ->withErrors([
                    'base_price' =>
                        'Effective price cannot be negative.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 12. Save Pricing
        |--------------------------------------------------------------------------
        */

        $pricing = RetailerPricing::create([

            'retailer_id' =>
                $data['retailer_id'],

            'warehouse_id' =>
                $data['warehouse_id'],

            'category_id' =>
                $data['category_id'],

            'product_id' =>
                $data['product_id'],

            'base_price' =>
                $data['base_price'],

            'discount_percent' =>
                $discountPercent,

            'discount_amount' =>
                $discountAmount,

            'effective_price' =>
                $effectivePrice,

            'effective_from' =>
                $data['effective_from'],

            'is_active' => 1,

        ]);


        /*
        |--------------------------------------------------------------------------
        | LOG 3 - Successfully Saved
        |--------------------------------------------------------------------------
        */

        \Log::info(
            'Retailer Pricing Store - Pricing Saved Successfully',
            [
                'pricing_id' => $pricing->id,
                'retailer_id' => $pricing->retailer_id,
                'warehouse_id' => $pricing->warehouse_id,
                'category_id' => $pricing->category_id,
                'product_id' => $pricing->product_id,
                'base_price' => $pricing->base_price,
                'discount_percent' => $pricing->discount_percent,
                'discount_amount' => $pricing->discount_amount,
                'effective_price' => $pricing->effective_price,
                'effective_from' => $pricing->effective_from,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 13. Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('retailer-pricing.index')
            ->with(
                'success',
                'Retailer pricing assigned successfully.'
            );
    }


    /**
     * Edit Retailer Pricing
     */
    public function edit(RetailerPricing $pricing)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | 1. Logged-in User
        |--------------------------------------------------------------------------
        */

        \Log::info('Retailer Pricing Edit - Request', [
            'user_id' => auth()->id(),
            'pricing_id' => $pricing->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. Logged-in User's Distribution Center
        |--------------------------------------------------------------------------
        */

        $warehouse = Warehouse::where('id', $user->warehouse_id)
            ->where('type', 'distribution_center')
            ->where('status', 'active')
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | 3. Security Check
        |--------------------------------------------------------------------------
        */

        if ((int) $pricing->warehouse_id !== (int) $warehouse->id) {

            \Log::warning(
                'Retailer Pricing Edit - Unauthorized Pricing',
                [
                    'pricing_id' => $pricing->id,
                    'pricing_warehouse_id' => $pricing->warehouse_id,
                    'user_warehouse_id' => $warehouse->id,
                ]
            );

            abort(403, 'You are not authorized to edit this pricing.');
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Only Logged-in User's DC
        |--------------------------------------------------------------------------
        */

        $warehouses = collect([$warehouse]);


        /*
        |--------------------------------------------------------------------------
        | 5. Retailers Of This DC
        |--------------------------------------------------------------------------
        */

        $retailers = Retailer::where('is_active', 1)
            ->where('shop_id', $warehouse->id)
            ->orderBy('name', 'asc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 6. Products Available In This DC
        |--------------------------------------------------------------------------
        */

        $productIds = WarehouseTransfer::where(
                'requested_by_warehouse_id',
                $warehouse->id
            )
            ->whereNotNull('product_id')
            ->pluck('product_id')
            ->unique();


        /*
        |--------------------------------------------------------------------------
        | 7. Categories Available In This DC
        |--------------------------------------------------------------------------
        */

        $categories = Category::whereHas('products', function ($query) use ($productIds) {

            $query->whereIn('id', $productIds);

        })
        ->orderBy('name', 'asc')
        ->get();


        /*
        |--------------------------------------------------------------------------
        | 8. Return Edit Form
        |--------------------------------------------------------------------------
        */

        return view(
            'retailer-pricing.form',
            compact(
                'pricing',
                'retailers',
                'warehouses',
                'categories',
                'warehouse'
            )
        );
    }


    /**
     * Update Retailer Pricing
     */
    public function update(
        Request $request,
        RetailerPricing $pricing
    ) {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate Request
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([

            'retailer_id' => [
                'required',
                'exists:retailers,id'
            ],

            'warehouse_id' => [
                'required',
                'exists:warehouses,id'
            ],

            'category_id' => [
                'required',
                'exists:categories,id'
            ],

            'product_id' => [
                'required',
                'exists:products,id'
            ],

            'base_price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'discount_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],

            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0'
            ],

            'effective_from' => [
                'required',
                'date'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | LOG 1 - Request
        |--------------------------------------------------------------------------
        */

        \Log::info(
            'Retailer Pricing Update - Request Data',
            [
                'pricing_id' => $pricing->id,
                'user_id' => auth()->id(),
                'data' => $data,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 2. Logged-in User
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | 3. Verify Distribution Center
        |--------------------------------------------------------------------------
        */

        $warehouse = Warehouse::where(
                'id',
                $data['warehouse_id']
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


        \Log::info(
            'Retailer Pricing Update - Warehouse Check',
            [
                'warehouse_found' => $warehouse ? true : false,
                'warehouse_id' => $warehouse?->id,
                'warehouse_name' => $warehouse?->name,
            ]
        );


        if (!$warehouse) {

            return back()
                ->withErrors([
                    'warehouse_id' =>
                        'Selected warehouse is not a valid active Distribution Center.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Security Check
        |--------------------------------------------------------------------------
        |
        | User can update pricing only in his assigned DC.
        |
        */

        if (
            (int) $warehouse->id !==
            (int) $user->warehouse_id
        ) {

            \Log::warning(
                'Retailer Pricing Update - Unauthorized DC',
                [
                    'user_warehouse_id' => $user->warehouse_id,
                    'selected_warehouse_id' => $warehouse->id,
                ]
            );


            return back()
                ->withErrors([
                    'warehouse_id' =>
                        'You are not authorized to use this Distribution Center.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Verify Existing Pricing Belongs To This DC
        |--------------------------------------------------------------------------
        */

        if (
            (int) $pricing->warehouse_id !==
            (int) $warehouse->id
        ) {

            \Log::warning(
                'Retailer Pricing Update - Pricing DC Mismatch',
                [
                    'pricing_id' => $pricing->id,
                    'pricing_warehouse_id' => $pricing->warehouse_id,
                    'warehouse_id' => $warehouse->id,
                ]
            );


            abort(
                403,
                'You are not authorized to update this pricing.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 6. Verify Retailer Belongs To This DC
        |--------------------------------------------------------------------------
        */

        $retailer = Retailer::where(
                'id',
                $data['retailer_id']
            )
            ->where(
                'is_active',
                1
            )
            ->where(
                'shop_id',
                $warehouse->id
            )
            ->first();


        \Log::info(
            'Retailer Pricing Update - Retailer Check',
            [
                'retailer_id' => $data['retailer_id'],
                'retailer_found' => $retailer ? true : false,
                'retailer_name' => $retailer?->name,
                'retailer_shop_id' => $retailer?->shop_id,
                'warehouse_id' => $warehouse->id,
            ]
        );


        if (!$retailer) {

            return back()
                ->withErrors([
                    'retailer_id' =>
                        'Selected retailer does not belong to this Distribution Center.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 7. Get Product
        |--------------------------------------------------------------------------
        */

        $product = Product::find(
            $data['product_id']
        );


        \Log::info(
            'Retailer Pricing Update - Product Check',
            [
                'product_id' => $data['product_id'],
                'product_found' => $product ? true : false,
                'product_name' => $product?->name,
                'product_category_id' => $product?->category_id,
                'selected_category_id' => $data['category_id'],
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 8. Verify Product Category
        |--------------------------------------------------------------------------
        */

        if (
            !$product ||
            (int) $product->category_id !==
            (int) $data['category_id']
        ) {

            return back()
                ->withErrors([
                    'product_id' =>
                        'Selected product does not belong to selected category.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 9. Verify Product Available In DC
        |--------------------------------------------------------------------------
        */

        $productExists = WarehouseTransfer::where(
                'requested_by_warehouse_id',
                $warehouse->id
            )
            ->where(
                'product_id',
                $data['product_id']
            )
            ->exists();


        \Log::info(
            'Retailer Pricing Update - Product DC Check',
            [
                'warehouse_id' => $warehouse->id,
                'product_id' => $data['product_id'],
                'product_available' => $productExists,
            ]
        );


        if (!$productExists) {

            return back()
                ->withErrors([
                    'product_id' =>
                        'Selected product is not available in this Distribution Center.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 10. Duplicate Pricing Check
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Current pricing ID ko exclude kar rahe hain.
        |
        */

        $exists = RetailerPricing::where(
                'retailer_id',
                $data['retailer_id']
            )
            ->where(
                'warehouse_id',
                $data['warehouse_id']
            )
            ->where(
                'category_id',
                $data['category_id']
            )
            ->where(
                'product_id',
                $data['product_id']
            )
            ->whereDate(
                'effective_from',
                $data['effective_from']
            )
            ->where(
                'id',
                '!=',
                $pricing->id
            )
            ->exists();


        \Log::info(
            'Retailer Pricing Update - Duplicate Check',
            [
                'duplicate_found' => $exists,
                'current_pricing_id' => $pricing->id,
                'retailer_id' => $data['retailer_id'],
                'warehouse_id' => $data['warehouse_id'],
                'category_id' => $data['category_id'],
                'product_id' => $data['product_id'],
                'effective_from' => $data['effective_from'],
            ]
        );


        if ($exists) {

            return back()
                ->withErrors([
                    'product_id' =>
                        'Pricing already exists for this retailer, DC, category and product.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 11. Calculate Discount
        |--------------------------------------------------------------------------
        */

        $discountPercent =
            $data['discount_percent'] ?? 0;


        $discountAmount =
            $data['discount_amount'] ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Discount % -> Amount
        |--------------------------------------------------------------------------
        */

        if (
            empty($discountAmount) &&
            !empty($discountPercent)
        ) {

            $discountAmount =
                (
                    $data['base_price'] *
                    $discountPercent
                ) / 100;
        }


        /*
        |--------------------------------------------------------------------------
        | 12. Calculate Effective Price
        |--------------------------------------------------------------------------
        */

        $effectivePrice =
            $data['base_price'] -
            $discountAmount;


        \Log::info(
            'Retailer Pricing Update - Price Calculation',
            [
                'base_price' => $data['base_price'],
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'effective_price' => $effectivePrice,
            ]
        );


        if ($effectivePrice < 0) {

            return back()
                ->withErrors([
                    'base_price' =>
                        'Effective price cannot be negative.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | 13. Update Pricing
        |--------------------------------------------------------------------------
        */

        $pricing->update([

            'retailer_id' =>
                $data['retailer_id'],

            'warehouse_id' =>
                $data['warehouse_id'],

            'category_id' =>
                $data['category_id'],

            'product_id' =>
                $data['product_id'],

            'base_price' =>
                $data['base_price'],

            'discount_percent' =>
                $discountPercent,

            'discount_amount' =>
                $discountAmount,

            'effective_price' =>
                $effectivePrice,

            'effective_from' =>
                $data['effective_from'],

        ]);


        /*
        |--------------------------------------------------------------------------
        | LOG 2 - Updated Successfully
        |--------------------------------------------------------------------------
        */

        \Log::info(
            'Retailer Pricing Update - Updated Successfully',
            [
                'pricing_id' => $pricing->id,
                'retailer_id' => $pricing->retailer_id,
                'warehouse_id' => $pricing->warehouse_id,
                'category_id' => $pricing->category_id,
                'product_id' => $pricing->product_id,
                'base_price' => $pricing->base_price,
                'discount_percent' => $pricing->discount_percent,
                'discount_amount' => $pricing->discount_amount,
                'effective_price' => $pricing->effective_price,
                'effective_from' => $pricing->effective_from,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 14. Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('retailer-pricing.index')
            ->with(
                'success',
                'Retailer pricing updated successfully.'
            );
    }


    /**
     * Delete Retailer Pricing
     */
    public function destroy($pricing)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Find Pricing
        |--------------------------------------------------------------------------
        */

        $pricing = RetailerPricing::find($pricing);

        /*
        |--------------------------------------------------------------------------
        | 2. Pricing Not Found
        |--------------------------------------------------------------------------
        */

        if (!$pricing) {

            \Log::warning(
                'Retailer Pricing Delete - Pricing Not Found',
                [
                    'pricing_id' => $pricing,
                    'user_id' => auth()->id(),
                ]
            );

            return redirect()
                ->route('retailer-pricing.index')
                ->with(
                    'error',
                    'Retailer pricing record not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Log Before Delete
        |--------------------------------------------------------------------------
        */

        \Log::info(
            'Retailer Pricing Delete - Request',
            [
                'pricing_id' => $pricing->id,
                'retailer_id' => $pricing->retailer_id,
                'warehouse_id' => $pricing->warehouse_id,
                'category_id' => $pricing->category_id,
                'product_id' => $pricing->product_id,
                'base_price' => $pricing->base_price,
                'effective_price' => $pricing->effective_price,
                'user_id' => auth()->id(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 4. Delete Pricing
        |--------------------------------------------------------------------------
        */

        $pricing->delete();


        /*
        |--------------------------------------------------------------------------
        | 5. Success Log
        |--------------------------------------------------------------------------
        */

        \Log::info(
            'Retailer Pricing Delete - Successfully Deleted',
            [
                'pricing_id' => $pricing->id,
                'user_id' => auth()->id(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 6. Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('retailer-pricing.index')
            ->with(
                'success',
                'Retailer pricing deleted successfully.'
            );
    }


}