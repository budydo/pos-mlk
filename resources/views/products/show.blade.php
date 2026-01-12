@extends('layouts.main')

@section('title','Detail Produk')

@section('content')
<div class="card">
    <h2 class="text-xl font-semibold">Detail Produk - {{ $product->name }}</h2>

    <div class="mt-4 grid grid-cols-2 gap-6">
        <div>
            <div class="text-sm text-gray-500">Kode</div>
            <div class="font-medium">{{ $product->code }}</div>
            <div class="mt-3 text-sm text-gray-500">Barcode</div>
            <div>{{ $product->barcode ?? '-' }}</div>
        </div>
        <div>
            <div class="text-sm text-gray-500">Harga modal</div>
            <div>Rp {{ number_format($product->cost_price,2) }}</div>
            <div class="mt-3 text-sm text-gray-500">Harga jual</div>
            <div>Rp {{ number_format($product->sell_price,2) }}</div>
            <div class="mt-3 text-sm text-gray-500">Stok</div>
            <div>{{ $product->stock }}</div>
        </div>
    </div>

    <div class="mt-4">
        @if(auth()->user()?->role === 'pemilik')
            <a href="{{ route('products.edit', $product->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Edit</a>
        @endif
        <a href="{{ route('products.index') }}" class="ml-2 text-gray-600">Kembali</a>
    </div>
</div>
@endsection