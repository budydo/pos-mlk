<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Data produk Indonesia dengan harga realistis
        $products = [
            [
                'code' => 'SKU001',
                'barcode' => '8991234000001',
                'name' => 'Indomie Mie Goreng',
                'category' => 'Makanan',
                'cost_price' => 1200,
                'sell_price' => 1500,
                'stock_qty' => 150,
            ],
            [
                'code' => 'SKU002',
                'barcode' => '8991234000002',
                'name' => 'Teh Botol Sosro',
                'category' => 'Minuman',
                'cost_price' => 4000,
                'sell_price' => 5500,
                'stock_qty' => 120,
            ],
            [
                'code' => 'SKU003',
                'barcode' => '8991234000003',
                'name' => 'Coca Cola 330ml',
                'category' => 'Minuman',
                'cost_price' => 5000,
                'sell_price' => 6500,
                'stock_qty' => 100,
            ],
            [
                'code' => 'SKU004',
                'barcode' => '8991234000004',
                'name' => 'Beras Pera 5kg',
                'category' => 'Bumbu & Bahan',
                'cost_price' => 45000,
                'sell_price' => 52000,
                'stock_qty' => 30,
            ],
            [
                'code' => 'SKU005',
                'barcode' => '8991234000005',
                'name' => 'Minyak Goreng Bimoli 1L',
                'category' => 'Bumbu & Bahan',
                'cost_price' => 12000,
                'sell_price' => 14500,
                'stock_qty' => 80,
            ],
            [
                'code' => 'SKU006',
                'barcode' => '8991234000006',
                'name' => 'Gula Pasir 1kg',
                'category' => 'Bumbu & Bahan',
                'cost_price' => 9500,
                'sell_price' => 11500,
                'stock_qty' => 60,
            ],
            [
                'code' => 'SKU007',
                'barcode' => '8991234000007',
                'name' => 'Roti Tawar Gardenia',
                'category' => 'Makanan',
                'cost_price' => 8000,
                'sell_price' => 10000,
                'stock_qty' => 45,
            ],
            [
                'code' => 'SKU008',
                'barcode' => '8991234000008',
                'name' => 'Susu Ultra Milk 1L',
                'category' => 'Minuman',
                'cost_price' => 9500,
                'sell_price' => 11500,
                'stock_qty' => 70,
            ],
            [
                'code' => 'SKU009',
                'barcode' => '8991234000009',
                'name' => 'Telur Ayam 1 Lusin',
                'category' => 'Makanan',
                'cost_price' => 18000,
                'sell_price' => 21000,
                'stock_qty' => 40,
            ],
            [
                'code' => 'SKU010',
                'barcode' => '8991234000010',
                'name' => 'Kopi Nescafé 200g',
                'category' => 'Minuman',
                'cost_price' => 22000,
                'sell_price' => 26500,
                'stock_qty' => 55,
            ],
        ];

        // Create products dengan stock entries
        foreach ($products as $data) {
            $costPrice = $data['cost_price'];
            $sellPrice = $data['sell_price'];
            $stockQty = $data['stock_qty'];

            // Create product
            $product = Product::create([
                'code' => $data['code'],
                'barcode' => $data['barcode'],
                'name' => $data['name'],
                'cost_price' => $costPrice,
                'sell_price' => $sellPrice,
                'stock' => $stockQty,
            ]);

            // Create stock entry untuk setiap produk
            StockEntry::create([
                'product_id' => $product->id,
                'quantity' => $stockQty,
                'purchase_price' => $costPrice,
            ]);
        }
    }
}
