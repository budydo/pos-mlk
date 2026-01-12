<?php

use Database\Seeders\ProductsSeeder;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\User;
use function Pest\Laravel\post;
use function Pest\Laravel\actingAs;

it('products seeder makes sell_price > cost_price for all products', function () {
    // run the specific seeder
    $this->seed(ProductsSeeder::class);

    $allOk = Product::all()->every(fn($p) => $p->sell_price > $p->cost_price);
    expect($allOk)->toBeTrue();
});

it('stock entry store will adjust sell_price if purchase_price exceeds sell_price', function () {
    $user = User::factory()->create(['role' => 'pemilik']);
    $product = Product::factory()->create(['cost_price' => 10, 'sell_price' => 12, 'stock' => 0, 'code' => 'TEST-' . uniqid()]);

    actingAs($user);
    $this->get('/stock-entries/create');
    $token = session('_token');

    // create entry with high purchase price
    post('/stock-entries', ['product_id' => $product->id, 'quantity' => 5, 'purchase_price' => 100, '_token' => $token])->assertRedirect();

    $product->refresh();
    expect($product->sell_price)->toBeGreaterThan($product->cost_price);
});

it('stock entry update will enforce sell_price > cost_price after edit', function () {
    $user = User::factory()->create(['role' => 'pemilik']);
    $product = Product::factory()->create(['cost_price' => 10, 'sell_price' => 20, 'stock' => 0, 'code' => 'TEST-' . uniqid()]);

    actingAs($user);
    $this->get('/stock-entries/create');
    $token = session('_token');

    post('/stock-entries', ['product_id' => $product->id, 'quantity' => 5, 'purchase_price' => 15, '_token' => $token])->assertRedirect();

    $entry = StockEntry::where('product_id', $product->id)->first();

    // update entry to higher purchase_price
    $this->get('/stock-entries/'.$entry->id.'/edit');
    $token2 = session('_token');

    post('/stock-entries/'.$entry->id, ['product_id' => $product->id, 'quantity' => 5, 'purchase_price' => 100, '_token' => $token2, '_method' => 'PUT'])->assertRedirect();

    $product->refresh();
    expect($product->sell_price)->toBeGreaterThan($product->cost_price);
});
