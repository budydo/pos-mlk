@extends('layouts.main')

@section('title','Stok')
@section('header', 'Manajemen Stok')

@section('content')
<div class="card-elevated">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold mb-1">📚 Entri Stok</h2>
            <p class="text-gray-600 text-sm">Kelola riwayat penambahan stok produk</p>
        </div>
        <div>
            <form method="GET" action="{{ route('stock-entries.index') }}" class="flex gap-2 items-center mb-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk / catatan" class="input input-sm">
                <input type="text" name="product_code" value="{{ request('product_code') }}" placeholder="Kode produk" class="input input-sm">
                <input type="text" name="product_name" value="{{ request('product_name') }}" placeholder="Nama produk" class="input input-sm">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="input input-sm">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="input input-sm">
                <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            </form>

            @if(auth()->user()?->role === 'pemilik')
                <a href="{{ route('stock-entries.create') }}" class="btn btn-primary no-underline">📥 Tambah Entri Stok</a>
            @endif
        </div>
    </div>

    @if($entries->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm table-striped">
            <thead class="bg-gray-50 border-b-2 border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Produk</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Jumlah</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-700">Harga Beli</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Tanggal</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $e)
                    <tr class="hover:bg-emerald-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-mono text-xs text-gray-500">{{ $e->product->code }}</div>
                            <div class="font-medium">{{ $e->product->name }}</div>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold">{{ $e->quantity }} unit</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($e->purchase_price, 0, '.', '.') }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $e->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 items-center justify-center flex-wrap">
                                @if(auth()->user()?->role === 'pemilik')
                                    <a href="{{ route('stock-entries.edit', $e->id) }}" class="text-blue-600 font-medium text-sm hover:underline no-underline">Edit</a>
                                    <form method="POST" action="{{ route('stock-entries.destroy', $e->id) }}" onsubmit="return confirm('Hapus entri stok ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 font-medium text-sm hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($entries->hasPages())
            <div class="mt-4">{{ $entries->links() }}</div>
        @endif
    </div>
    @else
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg mb-4">📚 Belum ada entri stok</p>
        @if(auth()->user()?->role === 'pemilik')
            <a href="{{ route('stock-entries.create') }}" class="btn btn-primary no-underline">Buat Entri Pertama</a>
        @endif
    </div>
    @endif
</div>
@endsection