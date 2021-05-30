<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Console\Command;

class InventoryManage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:manage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // purchase inventory
        $purchases = Purchase::with(['items'])->where('is_lock', false)->get();
        if ($purchases) {
            foreach ($purchases as $purchase) {
                if ($purchase->items) {
                    foreach ($purchase->items as $item) {
                        Inventory::create([
                            'item_id' => $item->item_id,
                            'type' => 'IN',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'detail' => 'from purchase'
                        ]);
                    }
                }
                $purchase->update([
                    'is_lock' => true
                ]);
            }
        }

        // sales inventory
        $sales = Sale::with(['items'])->where('is_lock', false)->get();
        if ($sales) {
            foreach ($sales as $sale) {
                if ($sale->items) {
                    foreach ($sale->items as $item) {
                        Inventory::create([
                            'item_id' => $item->item_id,
                            'type' => 'OUT',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'detail' => 'from sales'
                        ]);
                    }
                }

                $sale->update([
                    'is_lock' => true
                ]);
            }
        }
    }
}
