<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerOrderReturn;
use App\Models\Order;
use App\Models\OrderItem;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CustomerProductReturnController extends Controller
{
    public function returnReasons()
    {
        return response()->json([
            'status' => true,
            'data' => [
                ['id' => 1, 'key' => 'damaged',       'label' => 'Product damaged'],
                ['id' => 2, 'key' => 'expired',       'label' => 'Expired product'],
                ['id' => 3, 'key' => 'wrong_item',    'label' => 'Wrong item delivered'],
                ['id' => 4, 'key' => 'missing_item',  'label' => 'Item missing in order'],
                ['id' => 5, 'key' => 'quality_issue', 'label' => 'Poor quality'],
            ]
        ]);
    }

    // public function createReturn(Request $request)
    // {
    //     try {

    //         Log::info('Return API Hit', [
    //             'customer_id' => auth()->id(),
    //             'payload' => $request->except('items.*.images')
    //         ]);

    //         // ✅ VALIDATION
    //         $validated = $request->validate([
    //             'order_id' => 'required|integer',
    //             'items' => 'required|array|min:1',

    //             'items.*.product_id' => 'required|integer',
    //             'items.*.quantity' => 'required|integer|min:1',
    //             'items.*.reason_id' => 'required|in:1,2,3,4,5',
    //             'items.*.images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
    //         ]);

    //         Log::info('Validation Passed', $validated);

    //         $reasonMap = [
    //             1 => 'damaged',
    //             2 => 'expired',
    //             3 => 'wrong_item',
    //             4 => 'missing_item',
    //             5 => 'quality_issue'
    //         ];

    //         $returns = [];

    //         DB::beginTransaction();

    //         foreach ($validated['items'] as $index => $item) {

    //             Log::info('Processing Item', $item);

    //             // 🔹 Find order item
    //             $orderItem = OrderItem::where('order_id', $validated['order_id'])
    //                 ->where('product_id', $item['product_id'])
    //                 ->first();

    //             if (!$orderItem) {

    //                 Log::warning('Product not found in order', [
    //                     'order_id' => $validated['order_id'],
    //                     'product_id' => $item['product_id']
    //                 ]);

    //                 DB::rollBack();
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Product not found in order'
    //                 ], 422);
    //             }

    //             // 🔹 Qty check
    //             $alreadyReturned = CustomerOrderReturn::where(
    //                 'order_item_id',
    //                 $orderItem->id
    //             )->sum('quantity');

    //             $returnable = $orderItem->quantity - $alreadyReturned;

    //             Log::info('Quantity Check', [
    //                 'ordered' => $orderItem->quantity,
    //                 'already_returned' => $alreadyReturned,
    //                 'returnable' => $returnable,
    //                 'requested' => $item['quantity']
    //             ]);

    //             if ($item['quantity'] > $returnable) {

    //                 Log::warning('Return qty exceeded', [
    //                     'product_id' => $item['product_id']
    //                 ]);

    //                 DB::rollBack();
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Return qty exceeds'
    //                 ], 422);
    //             }

    //             // 🔹 Image Upload
    //             $images = [];
    //             if ($request->hasFile("items.$index.images")) {
    //                 foreach ($request->file("items.$index.images") as $file) {
    //                     $path = $file->store('returns', 'public');
    //                     $images[] = $path;
    //                 }

    //                 Log::info('Images Uploaded', $images);
    //             }

    //             // 🔹 Create return
    //             $return = CustomerOrderReturn::create([
    //                 'order_id' => $validated['order_id'],
    //                 'order_item_id' => $orderItem->id,
    //                 'product_id' => $item['product_id'],
    //                 'customer_id' => auth()->id(),
    //                 'quantity' => $item['quantity'],
    //                 'reason' => $reasonMap[$item['reason_id']],
    //                 'product_images' => json_encode($images),
    //                 'status' => 'requested',
    //                 'qc_status' => 'pending'
    //             ]);

    //             Log::info('Return Created', [
    //                 'return_id' => $return->id,
    //                 'product_id' => $item['product_id']
    //             ]);

    //             $returns[] = $return;
    //         }

    //         DB::commit();

    //         Log::info('Return API Success', [
    //             'customer_id' => auth()->id(),
    //             'returns_count' => count($returns)
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Return created with images',
    //             'data' => $returns
    //         ]);
    //     } catch (\Throwable $e) {

    //         DB::rollBack();

    //         Log::error('Return API Failed', [
    //             'error' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //             'customer_id' => auth()->id()
    //         ]);

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong'
    //         ], 500);
    //     }
    // }

    public function createReturn(Request $request)
    {
        try {

            $user = $request->user();

            // ✅ Validate Request
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'items' => 'required|array|min:1',

                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.reason_id' => 'required|in:1,2,3,4,5',
                'items.*.images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            // ✅ Reason Mapping
            $reasonMap = [
                1 => 'damaged',
                2 => 'expired',
                3 => 'wrong_item',
                4 => 'missing_item',
                5 => 'quality_issue'
            ];

            $returns = [];

            DB::beginTransaction();

            foreach ($validated['items'] as $index => $item) {

                // 🔒 Get Order Item
                $orderItem = OrderItem::where('order_id', $validated['order_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();

                if (!$orderItem) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Product not found in order'
                    ], 422);
                }

                // 🔒 Ownership Check (IMPORTANT)
                if ($orderItem->order->user_id !== $user->id) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized'
                    ], 403);
                }

                // 🔥 Calculate already returned quantity
                $alreadyReturned = CustomerOrderReturn::where('order_item_id', $orderItem->id)
                    ->whereIn('status', ['requested', 'approved', 'picked', 'completed'])
                    ->sum('quantity');

                $returnableQty = $orderItem->quantity - $alreadyReturned;

                if ($item['quantity'] > $returnableQty) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Return quantity exceeds available quantity'
                    ], 422);
                }

                // 🖼 Upload Images
                $imagePaths = [];
                if ($request->hasFile("items.$index.images")) {
                    foreach ($request->file("items.$index.images") as $file) {

                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                        $path = $file->storeAs('returns', $filename, 'public');

                        $imagePaths[] = $path;
                    }
                }

                // ✅ Create Return Request (NO quantity update here)
                $return = CustomerOrderReturn::create([
                    'order_id' => $validated['order_id'],
                    'order_item_id' => $orderItem->id,
                    'product_id' => $item['product_id'],
                    'customer_id' => $user->id,
                    'quantity' => $item['quantity'],
                    'reason' => $reasonMap[$item['reason_id']],
                    'product_images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
                    'status' => 'requested',   // 🔥 important
                    'qc_status' => 'pending'
                ]);

                $returns[] = $return;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Return request created successfully',
                'data' => $returns
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


public function getOrderReturnProducts($orderId)
{
    try {

        Log::info('Get Order Return Products API Hit', [
            'order_id' => $orderId
        ]);

        $orderItems = OrderItem::with('product:id,name,final_price')
            ->where('order_id', $orderId)
            ->get();

        Log::info('Order Items Fetched', [
            'count' => $orderItems->count(),
            'sample' => $orderItems->take(1)
        ]);

        if ($orderItems->isEmpty()) {

            Log::warning('No order items found', [
                'order_id' => $orderId
            ]);

            return response()->json([
                'status' => false,
                'message' => 'No items found for this order',
                'data' => []
            ]);
        }

        $data = $orderItems->map(function ($item) {

            Log::info('Processing Order Item', [
                'order_item_id' => $item->id,
                'product_id' => $item->product_id
            ]);

            // 🔹 Already returned quantity
            $returnedQty = CustomerOrderReturn::where('order_item_id', $item->id)
                ->sum('quantity');

            $returnableQty = max(0, $item->quantity - $returnedQty);

            Log::info('Quantity Calculation', [
                'order_item_id' => $item->id,
                'ordered_qty' => $item->quantity,
                'returned_qty' => $returnedQty,
                'returnable_qty' => $returnableQty
            ]);

            // 🔹 Fetch return images
            $returns = CustomerOrderReturn::where('order_item_id', $item->id)
                ->whereNotNull('product_images')
                ->get();

            Log::info('Return Records Found', [
                'order_item_id' => $item->id,
                'count' => $returns->count(),
                'raw_data' => $returns->pluck('product_images')
            ]);

            $returnImages = $returns->flatMap(function ($return) {

                Log::info('Processing Return Image', [
                    'return_id' => $return->id,
                    'raw_image' => $return->getRawOriginal('product_images'),
                    'processed_image' => $return->product_images
                ]);

                if (is_string($return->product_images)) {
                    $decoded = json_decode($return->product_images, true);

                    Log::info('Decoded JSON Image', [
                        'decoded' => $decoded
                    ]);

                    return $decoded ?? [];
                }

                if (is_array($return->product_images)) {
                    return $return->product_images;
                }

                return [];
            })->values();

            $imageUrls = $returnImages->map(function ($img) {

                if (filter_var($img, FILTER_VALIDATE_URL)) {
                    return $img;
                }

                return asset('storage/' . ltrim($img, '/'));
            });

            Log::info('Final Image URLs', [
                'order_item_id' => $item->id,
                'images' => $imageUrls
            ]);

            return [
                'order_item_id'   => $item->id,
                'product_id'      => $item->product_id,
                'product_name'    => $item->product->name ?? null,
                'ordered_qty'     => $item->quantity,
                'returnable_qty'  => $returnableQty,
                'price'           => $item->price,

                'return_image_urls' => $imageUrls
            ];
        });

        Log::info('Final API Response Ready', [
            'order_id' => $orderId,
            'total_items' => count($data)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Returnable products fetched',
            'data' => $data
        ]);

    } catch (\Throwable $e) {

        Log::error('Get Order Return Products API Failed', [
            'order_id' => $orderId,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong'
        ], 500);
    }
}


    // public function getOrderReturnProducts($orderId)
    // {
    //     $returns = CustomerOrderReturn::with([
    //         'product:id,name,final_price',
    //         'orderItem:id,order_id,quantity'
    //     ])
    //         ->where('order_id', $orderId)
    //         ->get();

    //     if ($returns->isEmpty()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No return products found',
    //             'data' => []
    //         ]);
    //     }

    //     $data = $returns->map(function ($return) {

    //         return [
    //             'return_id'        => $return->id,
    //             'order_id'         => $return->order_id,
    //             'order_item_id'    => $return->order_item_id,
    //             'product_id'       => $return->product_id,
    //             'product_name'     => optional($return->product)->name,
    //             'price'            => optional($return->product)->final_price,

    //             'quantity'         => $return->quantity,
    //             'reason'           => $return->reason,
    //             'return_type'      => $return->return_type,
    //             'status'           => $return->status,
    //             'qc_status'        => $return->qc_status,

    //             // 🔥 THIS WILL AUTO RETURN IMAGE URL
    //             'product_images'   => $return->product_images,

    //             'created_at'       => $return->created_at,
    //         ];
    //     });

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Return products fetched successfully',
    //         'data' => $data
    //     ]);
    // }

    // product list for stock return
    public function productsList(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->where('id', $request->order_id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $products = $order->items->map(function ($item) {

            // Handle null safety
            if (!$item->product) {
                return null;
            }

            $images = is_array($item->product->product_images)
                ? $item->product->product_images
                : json_decode($item->product->product_images, true);

            return [
                'product_id' => $item->product->id,
                'name' => $item->product->name,

                'image' => !empty($images)
                    ? collect($images)->map(function ($img) {
                        return asset('storage/products/' . $img);
                    })
                    : [],

                'quantity' => $item->quantity,
            ];
        })->filter()->values(); // remove nulls

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_date' => $order->created_at,
            'products' => $products
        ]);
    }
}
