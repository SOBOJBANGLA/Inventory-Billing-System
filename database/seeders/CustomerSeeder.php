<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Smith',
                'phone' => '+1-555-0101',
                'email' => 'john.smith@email.com',
                'address' => '123 Main St, New York, NY 10001',
            ],
            [
                'name' => 'Sarah Johnson',
                'phone' => '+1-555-0102',
                'email' => 'sarah.johnson@email.com',
                'address' => '456 Oak Ave, Los Angeles, CA 90210',
            ],
            [
                'name' => 'Mike Wilson',
                'phone' => '+1-555-0103',
                'email' => 'mike.wilson@email.com',
                'address' => '789 Pine Rd, Chicago, IL 60601',
            ],
            [
                'name' => 'Emily Davis',
                'phone' => '+1-555-0104',
                'email' => 'emily.davis@email.com',
                'address' => '321 Elm St, Houston, TX 77001',
            ],
            [
                'name' => 'David Brown',
                'phone' => '+1-555-0105',
                'email' => 'david.brown@email.com',
                'address' => '654 Maple Dr, Phoenix, AZ 85001',
            ],
            [
                'name' => 'Lisa Anderson',
                'phone' => '+1-555-0106',
                'email' => 'lisa.anderson@email.com',
                'address' => '987 Cedar Ln, Philadelphia, PA 19101',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
