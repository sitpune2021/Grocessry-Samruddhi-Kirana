<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use App\Models\Order;
use App\Models\SubCategory;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosOrderController extends Controller
{
    public function create()
    {

        return view('pos.create');
    }


    public function store(Request $request)
    {
        $isAjax = $request->ajax() || $request->wantsJson();

        $request->validate([
            'items' => 'required',
            'payment_method' => 'required|in:cash,upi,card',
            'discount' => 'nullable|numeric|min:0',
            'customer_id' => 'nullable|exists:users,id',
        ]);

        $items = $request->items;

        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (!$items || count($items) === 0) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Cart is empty'
                ], 422);
            }

            return back()->withErrors(['items' => 'Cart is empty']);
        }

        $discount = (float) $request->discount;
        $customerId = $request->customer_id;

        $user = Auth::user();

        if (!$user->warehouse_id) {
            abort(400, 'User warehouse not assigned');
        }

        $orderData = [
            'order_number' => 'POS-' . now()->timestamp,
            'channel'      => 'pos',
            'order_type'   => 'walkin',
            'warehouse_id' => $user->warehouse_id,
            'user_id'      => $customerId,
            'items'        => [],
            'discount'     => $discount,
            'payment'      => [
                'method' => $request->payment_method
            ]
        ];

        foreach ($items as $item) {

            $product = Product::where('id', $item['product_id'])
                ->whereNull('deleted_at')
                ->firstOrFail();


            $orderData['items'][] = [
                'product_id'  => $product->id,
                'qty'         => $item['qty'],
                'price'       => $product->final_price,
                'tax_percent' => $product->gst_percentage ?? 0
            ];
        }

        // dd($orderData);
        $order = app(OrderService::class)->create($orderData, $user);


        if (in_array($request->payment_method, ['upi', 'card'])) {
            return response()->json([
                'order_id' => $order->id,
                'amount'   => $order->total_amount
            ]);
        }

        // CASH FLOW
        if ($isAjax) {
            return response()->json([
                'order_id' => $order->id,
                'redirect' => route('pos.invoice', $order->id)
            ]);
        }

        return redirect()
            ->route('pos.invoice', $order->id)
            ->with('success', 'Order completed successfully');
    }
    public function searchProducts(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $user = Auth::user();

        if (!$user || !$user->warehouse_id) {
            return response()->json([]);
        }

        try {

            $products = DB::table('products as p')
                ->leftJoin('units as u', 'u.id', '=', 'p.unit_id')
                ->whereNull('p.deleted_at')

                ->where(function ($q) use ($request) {
                    $q->where('p.name', 'like', "%{$request->q}%")
                        ->orWhere('p.sku', 'like', "%{$request->q}%");
                })

                ->whereExists(function ($q) use ($user) {
                    $q->select(DB::raw(1))
                        ->from('product_batches as pb')
                        ->whereColumn('pb.product_id', 'p.id')
                        ->where('pb.warehouse_id', $user->warehouse_id)
                        ->where('pb.quantity', '>', 0)
                        ->where(function ($exp) {
                            $exp->whereNull('pb.expiry_date')
                                ->orWhere('pb.expiry_date', '>=', now());
                        });
                })

                ->select(
                    'p.id',
                    'p.name',
                    'p.mrp',
                    'p.final_price',
                    'p.gst_percentage',
                    'p.unit_value',
                    'u.short_name as unit'
                )
                ->orderBy('p.name')
                ->limit(20)
                ->get();

            return response()->json($products);
        } catch (\Throwable $e) {

            Log::error('POS Search Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([], 500);
        }
    }

    public function productByBarcode($code)
    {
        $user = Auth::user();

        $product = DB::table('products')
            ->leftJoin('units', 'units.id', '=', 'products.unit_id')
            ->whereNull('products.deleted_at')

            ->where(function ($q) use ($code) {
                $q->where('products.barcode', $code)
                    ->orWhere('products.sku', $code);
            })

            ->whereExists(function ($q) use ($user) {
                $q->select(DB::raw(1))
                    ->from('product_batches')
                    ->whereColumn('product_batches.product_id', 'products.id')
                    ->where('product_batches.warehouse_id', $user->warehouse_id)
                    ->where('product_batches.quantity', '>', 0)
                    ->where(function ($exp) {
                        $exp->whereNull('product_batches.expiry_date')
                            ->orWhere('product_batches.expiry_date', '>=', now());
                    });
            })

            ->select(
                'products.id',
                'products.name',
                'products.mrp',
                'products.final_price',
                'products.gst_percentage',
                'products.unit_value',
                'units.short_name as unit'
            )
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Product not found or out of stock'
            ], 404);
        }

        return response()->json($product);
    }

    public function searchCustomers(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $cashier = Auth::user();

        $customers = DB::table('users as u')
            ->join('roles as r', 'r.id', '=', 'u.role_id')   // 👈 dynamic roles
            ->where('r.name', 'customer')                    // 👈 customer role
            ->whereNotNull('u.warehouse_id')                 // 👈 ignore NULL
            ->where('u.warehouse_id', $cashier->warehouse_id)
            ->whereNull('u.deleted_at')
            ->where(function ($q) use ($request) {
                $q->where('u.first_name', 'like', "%{$request->q}%")
                    ->orWhere('u.last_name', 'like', "%{$request->q}%")
                    ->orWhere('u.mobile', 'like', "%{$request->q}%");
            })
            ->select(
                'u.id',
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as name"),
                'u.mobile'
            )
            ->orderBy('u.first_name')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }



    public function invoice(Order $order)
    {
        $order->load(['items.product', 'payment']);
        return view('pos.invoice', compact('order'));
    }
}
