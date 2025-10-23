<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $customers = Customer::all();
        $products = Product::all();

        // Create sample invoices
        for ($i = 0; $i < 5; $i++) {
            $customer = $customers->random();
            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'total' => 0,
                'created_by' => $user->id,
            ]);

            $total = 0;
            $itemCount = rand(1, 4);
            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $subtotal = $product->price * $quantity;
                $total += $subtotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                // Update product quantity
                $product->decrement('quantity', $quantity);
            }

            $invoice->update(['total' => $total]);
        }
    }
}
