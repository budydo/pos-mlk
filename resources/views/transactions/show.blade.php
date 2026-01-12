@extends('layouts.main')

@section('title','Detail Transaksi')

@section('content')
<div class="card-elevated">
    <h2 class="text-xl font-semibold mb-2">Detail Transaksi #{{ str_pad($transaction->id,6,'0',STR_PAD_LEFT) }}</h2>
    <p class="text-sm text-gray-500 mb-4">Oleh: {{ optional($transaction->user)->name ?? '—' }} • {{ $transaction->created_at->format('d M Y H:i') }}</p>

    <div class="overflow-x-auto">
        <table class="w-full text-sm table-striped">
            <thead class="bg-gray-50 border-b-2 border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Nama Barang</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Qty</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-700">Harga</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-700">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $it)
                <tr>
                    <td class="px-4 py-3">{{ optional($it->product)->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">{{ $it->quantity }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($it->sell_price,0,'.','.') }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($it->item_total,0,'.','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-right">
        <p class="text-lg font-semibold">Total: Rp {{ number_format($transaction->total ?? 0,0,'.','.') }}</p>
        <p class="text-sm text-gray-600">Dibayar: Rp {{ number_format($transaction->paid_amount ?? 0,0,'.','.') }} • Kembalian: Rp {{ number_format($transaction->change ?? 0,0,'.','.') }}</p>
    </div>
</div>
@endsection