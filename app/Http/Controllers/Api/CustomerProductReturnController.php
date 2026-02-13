<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerOrderReturn;
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

    public function createReturn(Request $request)
    {
        try {

            Log::info('Return API Hit', [
                'customer_id' => auth()->id(),
                'payload' => $request->except('items.*.images')
            ]);

            // ✅ VALIDATION
            $validated = $request->validate([
                'order_id' => 'required|integer',
                'items' => 'required|array|min:1',

                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.reason_id' => 'required|in:1,2,3,4,5',
                'items.*.images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
            ]);

            Log::info('Validation Passed', $validated);

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

                Log::info('Processing Item', $item);

                // 🔹 Find order item
                $orderItem = OrderItem::where('order_id', $validated['order_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();

                if (!$orderItem) {

                    Log::warning('Product not found in order', [
                        'order_id' => $validated['order_id'],
                        'product_id' => $item['product_id']
                    ]);

                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Product not found in order'
                    ], 422);
                }

                // 🔹 Qty check
                $alreadyReturned = CustomerOrderReturn::where(
                    'order_item_id',
                    $orderItem->id
                )->sum('quantity');

                $returnable = $orderItem->quantity - $alreadyReturned;

                Log::info('Quantity Check', [
                    'ordered' => $orderItem->quantity,
                    'already_returned' => $alreadyReturned,
                    'returnable' => $returnable,
                    'requested' => $item['quantity']
                ]);

                if ($item['quantity'] > $returnable) {

                    Log::warning('Return qty exceeded', [
                        'product_id' => $item['product_id']
                    ]);

                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Return qty exceeds'
                    ], 422);
                }

                // 🔹 Image Upload
                $images = [];
                if ($request->hasFile("items.$index.images")) {
                    foreach ($request->file("items.$index.images") as $file) {
                        $path = $file->store('returns', 'public');
                        $images[] = $path;
                    }

                    Log::info('Images Uploaded', $images);
                }

                // 🔹 Create return
                $return = CustomerOrderReturn::create([
                    'order_id' => $validated['order_id'],
                    'order_item_id' => $orderItem->id,
                    'product_id' => $item['product_id'],
                    'customer_id' => auth()->id(),
                    'quantity' => $item['quantity'],
                    'reason' => $reasonMap[$item['reason_id']],
                    'product_images' => json_encode($images),
                    'status' => 'requested',
                    'qc_status' => 'pending'
                ]);

                Log::info('Return Created', [
                    'return_id' => $return->id,
                    'product_id' => $item['product_id']
                ]);

                $returns[] = $return;
            }

            DB::commit();

            Log::info('Return API Success', [
                'customer_id' => auth()->id(),
                'returns_count' => count($returns)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Return created with images',
                'data' => $returns
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Return API Failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'customer_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    // public function createReturn(Request $request)
    // {
    //     try {
    //         Log::info('Return request received', [
    //             'customer_id' => auth()->id(),
    //             'payload' => $request->all()
    //         ]);

    //         // 🔹 VALIDATION
    //         $validated = $request->validate([
    //             'order_id'        => 'required|integer',
    //             // 'order_item_id'   => 'required|integer',
    //             'product_id'      => 'required|integer',
    //             'quantity'        => 'required|integer|min:1',
    //             'reason_id'       => 'required|in:1,2,3,4,5',
    //             // 'return_type'     => 'required|in:refund,exchange',
    //             'product_images'   => 'nullable|array',
    //             // 'product_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    //         ]);

    //         // 🔹 REASON MAP (ID → STRING)
    //         $reasonMap = [
    //             1 => 'damaged',
    //             2 => 'expired',
    //             3 => 'wrong_item',
    //             4 => 'missing_item',
    //             5 => 'quality_issue',
    //         ];

    //         $reasonKey = $reasonMap[$validated['reason_id']];

    //         // 🔹 VERIFY ORDER ITEM (SECURITY CHECK)
    //         $orderItem = OrderItem::where('id', $validated['order_item_id'])
    //             ->where('order_id', $validated['order_id'])
    //             ->where('product_id', $validated['product_id'])
    //             ->first();

    //         if (!$orderItem) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Invalid order item'
    //             ], 422);
    //         }

    //         // 🔹 CHECK RETURNABLE QUANTITY
    //         $alreadyReturned = CustomerOrderReturn::where('order_item_id', $orderItem->id)
    //             ->sum('quantity');

    //         $maxReturnableQty = $orderItem->quantity - $alreadyReturned;

    //         if ($validated['quantity'] > $maxReturnableQty) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Return quantity exceeds ordered quantity'
    //             ], 422);
    //         }

    //         // 🔹 HANDLE PRODUCT IMAGES
    //         $images = [];
    //         if ($request->hasFile('product_images')) {
    //             foreach ($request->file('product_images') as $file) {
    //                 $images[] = $file->store('returns', 'public');
    //             }
    //         }

    //         // 🔹 CREATE RETURN REQUEST
    //         $return = CustomerOrderReturn::create([
    //             'order_id'        => $validated['order_id'],
    //             // 'order_item_id'   => $validated['order_item_id'],
    //             'product_id'      => $validated['product_id'],
    //             'customer_id'     => auth()->id(),
    //             'quantity'        => $validated['quantity'],
    //             'reason'          => $reasonKey,
    //             // 'return_type'     => $validated['return_type'],
    //             'status'          => 'requested',
    //             'qc_status'       => 'pending',
    //             'product_images'  => $images ?: null
    //         ]);

    //         // 🔹 SUCCESS RESPONSE
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Return request created successfully',
    //             'data' => [
    //                 'id'            => $return->id,
    //                 'order_id'      => $return->order_id,
    //                 // 'order_item_id' => $return->order_item_id,
    //                 'product_id'    => $return->product_id,
    //                 'quantity'      => $return->quantity,
    //                 'reason_id'     => $validated['reason_id'], // sent back
    //                 'reason'        => $return->reason,
    //                 // 'return_type'   => $return->return_type,
    //                 'status'        => $return->status,
    //                 'qc_status'     => $return->qc_status,
    //                 'product_images' => $return->product_images
    //             ]
    //         ], 200);
    //     } catch (\Throwable $e) {

    //         Log::error('Return request failed', [
    //             'error' => $e->getMessage(),
    //             'customer_id' => auth()->id(),
    //             'payload' => $request->all()
    //         ]);

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong'
    //         ], 500);
    //     }
    // }

    public function getOrderReturnProducts($orderId)
    {
        $orderItems = OrderItem::with('product:id,name,final_price')
            ->where('order_id', $orderId)
            ->get();

        if ($orderItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No items found for this order',
                'data' => []
            ]);
        }

        $data = $orderItems->map(function ($item) {

            // 🔹 Already returned quantity
            $returnedQty = CustomerOrderReturn::where('order_item_id', $item->id)
                ->sum('quantity');

            $returnableQty = max(0, $item->quantity - $returnedQty);

            // 🔹 Fetch return images (NOT product images)
            $returnImages = CustomerOrderReturn::where('order_item_id', $item->id)
                ->whereNotNull('product_images')
                ->get()
                ->flatMap(function ($return) {
                    if (is_string($return->product_images)) {
                        return json_decode($return->product_images, true) ?? [];
                    }

                    if (is_array($return->product_images)) {
                        return $return->product_images;
                    }

                    return [];
                })
                ->values();


            return [
                'order_item_id'   => $item->id,
                'product_id'      => $item->product_id,
                'product_name'    => $item->product->name,
                'ordered_qty'     => $item->quantity,
                'returnable_qty'  => $returnableQty,
                'price'           => $item->price,

                'return_image_urls' => $returnImages->map(function ($img) {

                    if (filter_var($img, FILTER_VALIDATE_URL)) {
                        return $img;
                    }
                    return asset('storage/' . ltrim($img, '/'));
                })

            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Returnable products fetched',
            'data' => $data
        ]);
    }

    
}
