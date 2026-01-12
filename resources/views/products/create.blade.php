@extends('layouts.main')

@section('title','Tambah Produk')
@section('header', 'Tambah Produk Baru')

@section('content')
<div class="max-w-2xl">
    <div class="card-elevated">
        <h2 class="text-2xl font-bold mb-6">📝 Tambah Produk Baru</h2>
        
        <form method="POST" action="{{ route('products.store') }}" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Barang *</label>
                <input 
                    name="code" 
                    placeholder="Contoh: PROD001" 
                    required 
                    value="{{ old('code') }}"
                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors @error('code') border-red-500 @enderror"
                />
                @error('code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Barcode (Opsional)</label>
                <input 
                    name="barcode" 
                    placeholder="Contoh: 8991234567890" 
                    value="{{ old('barcode') }}"
                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Barang *</label>
                <input 
                    name="name" 
                    placeholder="Contoh: Mie Instan Rasa Ayam" 
                    required 
                    value="{{ old('name') }}"
                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors @error('name') border-red-500 @enderror"
                />
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Modal (Rp) *</label>
                    <input 
                        name="cost_price" 
                        type="number" 
                        placeholder="Contoh: 5000" 
                        required 
                        value="{{ old('cost_price') }}"
                        class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors @error('cost_price') border-red-500 @enderror"
                    />
                    @error('cost_price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual (Rp) *</label>
                    <input 
                        name="sell_price" 
                        type="number" 
                        placeholder="Contoh: 7500" 
                        required 
                        value="{{ old('sell_price') }}"
                        class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors @error('sell_price') border-red-500 @enderror"
                    />
                    @error('sell_price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stok Awal (Opsional)</label>
                <input 
                    name="stock" 
                    type="number" 
                    placeholder="Contoh: 100" 
                    value="{{ old('stock', 0) }}"
                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors"
                />
            </div>

            <div class="flex gap-3 pt-6 border-t-2 border-gray-200">
                <button type="submit" class="btn btn-primary px-8 no-underline">
                    ✓ Simpan Produk
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary px-8 no-underline">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection