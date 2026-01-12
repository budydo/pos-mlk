@extends('layouts.main')

@section('title','Produk')
@section('header', 'Manajemen Produk')

@section('content')
<div class="card-elevated">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold mb-1">📦 Daftar Produk</h2>
            <p class="text-gray-600 text-sm">Kelola semua produk dan stok Anda</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()?->role === 'pemilik')
                <a href="{{ route('products.create') }}" class="btn btn-primary no-underline">+ Tambah Produk</a>
                <a href="{{ route('stock-entries.create') }}" class="btn btn-secondary no-underline">📥 Tambah Stok</a>
            @endif
        </div>
    </div>

    @if($products->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm table-striped">
            <thead class="bg-gray-50 border-b-2 border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Kode</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Nama Produk</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-700">Harga Jual</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-700">Harga Beli Terakhir</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Stok</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                    <tr class="hover:bg-emerald-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-gray-700">{{ $p->code }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('products.show', $p->id) }}" class="text-accent-green font-medium hover:underline no-underline">{{ $p->name }}</a>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($p->sell_price, 0, '.', '.') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-700">Rp {{ number_format($p->last_purchase_price, 0, '.', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($p->stock <= 0)
                                <span class="badge badge-danger">Habis</span>
                            @elseif($p->stock < 20)
                                <span class="badge badge-warning">{{ $p->stock }} unit</span>
                            @else
                                <span class="badge badge-success">{{ $p->stock }} unit</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 items-center justify-center flex-wrap">
                                <a href="{{ route('products.show', $p->id) }}" class="text-accent-green font-medium text-sm hover:underline no-underline">Lihat</a>
                                @if(auth()->user()?->role === 'pemilik')
                                    <a href="{{ route('products.edit', $p->id) }}" class="text-blue-600 font-medium text-sm hover:underline no-underline">Edit</a>
                                    <form action="{{ route('products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 font-medium text-sm hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg mb-4">📦 Tidak ada produk</p>
        @if(auth()->user()?->role === 'pemilik')
            <a href="{{ route('products.create') }}" class="btn btn-primary no-underline">Tambah Produk Pertama</a>
        @endif
    </div>
    @endif
</div>
@endsection