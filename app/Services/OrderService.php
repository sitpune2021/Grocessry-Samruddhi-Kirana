<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Services\FifoStockService;

class OrderService
{
    public function create(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {

            /* ---------------- ORDER ---------------- */
            $order = Order::create([
                'order_number'   => $data['order_number'],
                'channel'        => $data['channel'],     // pos
                'order_type'     => $data['order_type'],  // walkin
                'user_id'        => $data['user_id'] ?? null,
                'warehouse_id'   => $data['warehouse_id'],
                'created_by'     => $user->id,
                'status'         => 'placed',
                'payment_status' => 'pending',
                'discount'       => $data['discount'] ?? 0,
            ]);

            $subtotal = 0;
            $gstTotal = 0;

            /* ---------------- ITEMS + FIFO ---------------- */
            foreach ($data['items'] as $item) {

                if ($item['qty'] <= 0) {
                    throw new \Exception('Invalid quantity');
                }

                $fifoBatches = app(FifoStockService::class)->consume(
                    $item['product_id'],
                    $data['warehouse_id'],
                    $item['qty'],
                    $order->id,
                    $user->id
                );

                foreach ($fifoBatches as $row) {

                    // Tax-inclusive pricing
                    $lineTotal = $row['qty'] * $item['price'];

                    // GST portion (derived, not added)
                    $taxAmount = ($item['tax_percent'] > 0)
                        ? $lineTotal * ($item['tax_percent'] / (100 + $item['tax_percent']))
                        : 0;

                    OrderItem::create([
                        'order_id'         => $order->id,
                        'product_id'       => $item['product_id'],
                        'product_batch_id' => $row['batch_id'],
                        'quantity'         => $row['qty'],
                        'price'            => $item['price'],
                        'tax_percent'      => $item['tax_percent'] ?? 0,
                        'tax_amount'       => $taxAmount,
                        'line_total'       => $lineTotal,
                        'total'            => $lineTotal, // IMPORTANT
                    ]);

                    $subtotal += $lineTotal;
                    $gstTotal += $taxAmount;
                }
            }

            /* ---------------- APPLY DISCOUNT ---------------- */
            $discount = min($data['discount'] ?? 0, $subtotal);
            $payable  = $subtotal - $discount;

            $order->update([
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'total_amount' => $payable,
            ]);

            /* ---------------- PAYMENT ---------------- */
            if (!empty($data['payment'])) {
                Payment::create([
                    'order_id'        => $order->id,
                    'user_id'         => $user->id,
                    'payment_gateway'=> $data['payment']['method'],
                    'amount'          => $payable, 
                    'status'          => 'success',
                ]);

                $order->update(['payment_status' => 'paid']);
            }

            return $order;
        });
    }
}
