@extends('layouts.main')

@section('title','Edit Produk')

@section('content')
<div class="card">
    <h2 class="text-xl font-semibold">Edit Produk</h2>

    <form method="POST" action="{{ route('products.update', $product->id) }}" class="mt-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <input name="code" value="{{ $product->code }}" class="border p-2 rounded" placeholder="Kode barang" required />
            <input name="barcode" value="{{ $product->barcode }}" class="border p-2 rounded" placeholder="Barcode (opsional)" />
            <input name="name" value="{{ $product->name }}" class="border p-2 rounded" placeholder="Nama" required />
            <input name="cost_price" value="{{ $product->cost_price }}" class="border p-2 rounded" placeholder="Harga modal" required />
            <input name="sell_price" value="{{ $product->sell_price }}" class="border p-2 rounded" placeholder="Harga jual" required />
            <input name="stock" value="{{ $product->stock }}" class="border p-2 rounded" placeholder="Stok" />
        </div>

        <div class="mt-4">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('products.index') }}" class="ml-2 text-gray-600">Batal</a>
        </div>
    </form>
</div>
@endsection