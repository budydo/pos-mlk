<?php

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Support\Str;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

it('paginates products at 10 per page and supports search and filters', function () {
    Product::factory()->count(25)->create();

    // specific product to find
    $target = Product::factory()->create(['name' => 'Unique Product X', 'code' => 'UNIQ-123']);

    // page 1 should contain 10 items
    get('/products')->assertOk()->assertViewHas('products', function ($products) {
        return $products->count() === 10;
    });

    // search by name
    get('/products?q=Unique Product X')->assertOk()->assertViewHas('products', function ($products) use ($target) {
        return $products->first()?->id === $target->id || $products->contains('id', $target->id);
    });

    // filter by code
    get('/products?code=UNIQ-123')->assertOk()->assertViewHas('products', function ($products) use ($target) {
        return $products->contains('id', $target->id);
    });
});

it('paginates stock entries and supports filters by product and date', function () {
    $product = Product::factory()->create(['name' => 'FilterProd', 'code' => 'FP-001']);

    // create 25 entries with different dates
    for ($i = 0; $i < 25; $i++) {
        StockEntry::factory()->create([
            'product_id' => $product->id,
            'created_at' => now()->subDays($i),
            'purchase_price' => 100 + $i,
        ]);
    }

    get('/stock-entries')->assertOk()->assertViewHas('entries', function ($entries) {
        return $entries->count() === 10;
    });

    // filter by product code
    get('/stock-entries?product_code=FP-001')->assertOk()->assertViewHas('entries', function ($entries) {
        return $entries->count() > 0;
    });

    // filter by date range for last 2 days
    $from = now()->subDays(1)->format('Y-m-d');
    $to = now()->format('Y-m-d');
    get("/stock-entries?date_from={$from}&date_to={$to}")->assertOk()->assertViewHas('entries', function ($entries) {
        return $entries->count() > 0;
    });
});

it('paginates transactions, supports filtering by product code and product name and date range', function () {
    $user = User::factory()->create(['role' => 'karyawan']);
    $productA = Product::factory()->create(['name' => 'TransProdA', 'code' => 'TP-A']);
    $productB = Product::factory()->create(['name' => 'TransProdB', 'code' => 'TP-B']);

    // create 15 transactions
    for ($i = 0; $i < 15; $i++) {
        $tx = Transaction::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDays($i)]);
        $prod = $i % 2 === 0 ? $productA : $productB;
        TransactionItem::create([
            'transaction_id' => $tx->id,
            'product_id' => $prod->id,
            'quantity' => 1,
            'sell_price' => $prod->sell_price,
            'cost_price' => $prod->cost_price,
            'item_total' => $prod->sell_price,
            'item_profit' => $prod->sell_price - $prod->cost_price,
            'created_at' => $tx->created_at,
            'updated_at' => $tx->updated_at,
        ]);
    }

    actingAs($user);

    get('/transactions')->assertOk()->assertViewHas('transactions', function ($transactions) {
        return $transactions->count() === 10;
    });

    // filter by product code
    get('/transactions?product_code=TP-A')->assertOk()->assertViewHas('transactions', function ($transactions) {
        return $transactions->count() > 0;
    });

    // filter by date range last 1 day
    $from = now()->subDays(1)->format('Y-m-d');
    $to = now()->format('Y-m-d');
    get("/transactions?date_from={$from}&date_to={$to}")->assertOk()->assertViewHas('transactions', function ($transactions) {
        return $transactions->count() > 0;
    });
});
