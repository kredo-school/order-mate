<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\OrderItemCustomOption;
use Exception;
use Illuminate\Support\Facades\Log;

class SplitOrderItems extends Command
{
    protected $signature = 'orders:split-order-items';
    protected $description = 'Split order_items with quantity > 1 into multiple rows (1 item per row) and distribute custom options.';

    public function handle()
    {
        $this->info('Start splitting order_items...');
        $items = OrderItem::with('customOptions')->where('quantity', '>', 1)->get();

        $this->info('Found ' . $items->count() . ' items to split.');

        foreach ($items as $orig) {
            DB::transaction(function () use ($orig) {
                $origQty = (int)$orig->quantity;
                if ($origQty <= 1) return;

                $origTotalPrice = (float)$orig->price;
                // 単価は四捨五入して2桁（通貨単位）
                $unitPrice = round($origTotalPrice / $origQty, 2);

                // collect option entries from original
                $origOptions = $orig->customOptions; // Collection of OrderItemCustomOption

                // Build per-item assignment arrays
                $assigned = [];
                $extraPriceMap = [];

                foreach ($origOptions as $op) {
                    $optId = $op->custom_option_id;
                    $extraPriceMap[$optId] = $op->extra_price;
                    $opQty = (int)$op->quantity;
                    for ($i = 0; $i < $opQty; $i++) {
                        $idx = $i % $origQty;
                        if (!isset($assigned[$idx])) $assigned[$idx] = [];
                        $assigned[$idx][$optId] = ($assigned[$idx][$optId] ?? 0) + 1;
                    }
                }

                // We'll reuse the original row as index 0; create others
                $newItems = [];
                for ($idx = 0; $idx < $origQty; $idx++) {
                    $itemPrice = $unitPrice;
                    $opts = $assigned[$idx] ?? [];
                    foreach ($opts as $optId => $optQty) {
                        $itemPrice += ($extraPriceMap[$optId] ?? 0) * $optQty;
                    }
                    // If idx == 0 update original, else create new
                    if ($idx === 0) {
                        $orig->quantity = 1;
                        $orig->price = $itemPrice;
                        $orig->save();

                        // delete old custom options for orig, we'll recreate
                        $orig->customOptions()->delete();

                        // recreate for idx 0
                        foreach ($opts as $optId => $optQty) {
                            OrderItemCustomOption::create([
                                'order_item_id' => $orig->id,
                                'custom_option_id' => $optId,
                                'quantity' => $optQty,
                                'extra_price' => $extraPriceMap[$optId] ?? 0,
                            ]);
                        }
                        $newItems[] = $orig;
                    } else {
                        $new = OrderItem::create([
                            'order_id' => $orig->order_id,
                            'menu_id'  => $orig->menu_id,
                            'quantity' => 1,
                            'price'    => $itemPrice,
                            'status'   => $orig->status,
                        ]);
                        foreach ($opts as $optId => $optQty) {
                            OrderItemCustomOption::create([
                                'order_item_id' => $new->id,
                                'custom_option_id' => $optId,
                                'quantity' => $optQty,
                                'extra_price' => $extraPriceMap[$optId] ?? 0,
                            ]);
                        }
                        $newItems[] = $new;
                    }
                }

                // 修正の微差を吸収：元の合計と新合計を揃える
                $sumNew = array_sum(array_map(function($it){ return (float)$it->price; }, $newItems));
                $diff = round($origTotalPrice - $sumNew, 2);
                if (abs($diff) > 0) {
                    // 最後のアイテムに差分を加える
                    $last = end($newItems);
                    $last->price = round($last->price + $diff, 2);
                    $last->save();
                }

                // 最後に、order の total_price を再計算して保存
                $orderModel = $orig->order()->first();
                if ($orderModel) {
                    $orderModel->total_price = $orderModel->orderItems()->sum('price');
                    $orderModel->save();
                }
            }, 5); // transaction retry attempts
            $this->info("Processed order_item id={$orig->id}");
        }

        $this->info('Done.');
        return Command::SUCCESS;
    }
}
