<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Dell XPS 13',
                'sku' => 'LAP001',
                'price' => 1299.99,
                'quantity' => 10,
                'description' => 'High-performance laptop with 13-inch display',
            ],
            [
                'name' => 'Wireless Mouse',
                'sku' => 'MOU001',
                'price' => 29.99,
                'quantity' => 50,
                'description' => 'Ergonomic wireless mouse with USB receiver',
            ],
            [
                'name' => 'Mechanical Keyboard',
                'sku' => 'KEY001',
                'price' => 89.99,
                'quantity' => 25,
                'description' => 'RGB mechanical keyboard with blue switches',
            ],
            [
                'name' => 'Monitor 24"',
                'sku' => 'MON001',
                'price' => 199.99,
                'quantity' => 15,
                'description' => '24-inch Full HD monitor with IPS panel',
            ],
            [
                'name' => 'USB-C Cable',
                'sku' => 'CAB001',
                'price' => 19.99,
                'quantity' => 100,
                'description' => 'High-speed USB-C cable 6ft length',
            ],
            [
                'name' => 'Webcam HD',
                'sku' => 'WEB001',
                'price' => 79.99,
                'quantity' => 30,
                'description' => '1080p HD webcam with built-in microphone',
            ],
            [
                'name' => 'Gaming Headset',
                'sku' => 'HEA001',
                'price' => 149.99,
                'quantity' => 20,
                'description' => '7.1 surround sound gaming headset',
            ],
            [
                'name' => 'External SSD 1TB',
                'sku' => 'SSD001',
                'price' => 199.99,
                'quantity' => 12,
                'description' => 'Portable SSD with USB 3.0 interface',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
