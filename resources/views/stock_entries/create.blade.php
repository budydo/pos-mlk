@extends('layouts.main')

@section('title','Tambah Entri Stok')

@section('content')
<div class="card max-w-md">
    <h2 class="text-xl font-semibold">Tambah Entri Stok</h2>
    <form method="POST" action="{{ route('stock-entries.store') }}" class="mt-4 grid gap-3">
        @csrf
        <select name="product_id" required class="border p-2 rounded">
            @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
            @endforeach
        </select>
        <input name="quantity" placeholder="Jumlah" required class="border p-2 rounded" />
        <input name="purchase_price" placeholder="Harga beli" required class="border p-2 rounded" />
        <input name="note" placeholder="Catatan (opsional)" class="border p-2 rounded" />
        <div>
            <button class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('stock-entries.index') }}" class="ml-2 text-gray-600">Batal</a>
        </div>
    </form>
</div>
@endsection