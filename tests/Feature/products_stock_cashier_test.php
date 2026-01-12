<?php

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('shows last purchase price on products list', function () {
    $user = User::factory()->create(['role' => 'pemilik']);
    $product = Product::factory()->create(['name' => 'Produk A', 'cost_price' => 100, 'sell_price' => 150]);

    // create a stock entry with purchase price 120
    StockEntry::factory()->create(['product_id' => $product->id, 'quantity' => 10, 'purchase_price' => 120]);

    actingAs($user);
    get('/products')->assertOk()->assertSee('Rp 120');
});

it('prevents checkout when product is out of stock', function () {
    $user = User::factory()->create(['role' => 'karyawan']);
    $product = Product::factory()->create(['stock' => 0, 'sell_price' => 100, 'code' => 'TEST-' . uniqid()]);

    actingAs($user);

    // initialize session & CSRF token
    $this->get('/profile');
    $token = session('_token');

    $payload = [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1]
        ],
        'paid_amount' => 100,
        '_token' => $token,
    ];

    post('/kasir/checkout', $payload)->assertStatus(422)->assertSee('Stok tidak cukup');
});

it('reduces stock when checkout succeeds', function () {
    $user = User::factory()->create(['role' => 'karyawan']);
    $product = Product::factory()->create(['stock' => 5, 'sell_price' => 100, 'cost_price' => 70, 'code' => 'TEST-' . uniqid()]);

    actingAs($user);

    // initialize session & CSRF token
    $this->get('/profile');
    $token = session('_token');

    $payload = [
        'items' => [ ['product_id' => $product->id, 'quantity' => 2] ],
        'paid_amount' => 200,
        '_token' => $token,
    ];

    post('/kasir/checkout', $payload)->assertStatus(200);

    $this->assertEquals(3, Product::find($product->id)->stock);
});

it('stock entry store updates product cost_price', function () {
    $user = User::factory()->create(['role' => 'pemilik']);
    $product = Product::factory()->create(['stock' => 0, 'cost_price' => 50, 'code' => 'TEST-' . uniqid()]);

    actingAs($user);

    // initialize session & CSRF token
    $this->get('/stock-entries/create');
    $token = session('_token');

    $data = ['product_id' => $product->id, 'quantity' => 10, 'purchase_price' => 200, '_token' => $token];

    post('/stock-entries', $data)->assertRedirect();

    $this->assertEquals(200, Product::find($product->id)->cost_price);
    $this->assertEquals(10, Product::find($product->id)->stock);
});
