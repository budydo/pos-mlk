@extends('layouts.main')

@section('title','Edit Entri Stok')

@section('content')
<div class="card">
    <h2 class="text-xl font-semibold">Edit Entri Stok</h2>

    <form method="POST" action="{{ route('stock-entries.update', $entry->id) }}" class="mt-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <select name="product_id" class="border p-2 rounded">
                @foreach($products as $p)
                    <option value="{{ $p->id }}" @if($entry->product_id == $p->id) selected @endif>{{ $p->code }} - {{ $p->name }}</option>
                @endforeach
            </select>
            <input name="quantity" value="{{ $entry->quantity }}" class="border p-2 rounded" placeholder="Jumlah" required />
            <input name="purchase_price" value="{{ $entry->purchase_price }}" class="border p-2 rounded" placeholder="Harga beli" required />
            <input name="note" value="{{ $entry->note }}" class="border p-2 rounded" placeholder="Catatan (opsional)" />
        </div>

        <div class="mt-4">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('stock-entries.index') }}" class="ml-2 text-gray-600">Batal</a>
        </div>
    </form>
</div>
@endsection