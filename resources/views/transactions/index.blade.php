@extends('layouts.main')

@section('title','Transaksi')
@section('header', 'Daftar Transaksi')

@section('content')
<div class="card-elevated">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold mb-1">📝 Daftar Transaksi</h2>
            <p class="text-gray-600 text-sm">Lihat riwayat semua transaksi penjualan</p>
        </div>
    </div>

    @if(isset($transactions) && $transactions->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm table-striped">
            <thead class="bg-gray-50 border-b-2 border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">No. Transaksi</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Item</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-700">Total</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Tanggal</th>
                    <th class="px-4 py-3 text-center font-bold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                    <tr class="hover:bg-emerald-50 transition-colors">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-900">#{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-center">{{ $t->items_count ?? 0 }} item</td>
                        <td class="px-4 py-3 text-right font-semibold text-accent-green">
                            Rp {{ number_format($t->total_amount ?? 0, 0, '.', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            {{ $t->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('transactions.show', $t->id) }}" class="text-accent-green font-medium text-sm hover:underline no-underline">Lihat Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg mb-4">📝 Belum ada transaksi</p>
        <p class="text-gray-600 text-sm">Mulai dengan melakukan transaksi di kasir</p>
        <a href="{{ route('cashier') }}" class="inline-block mt-4 btn btn-primary no-underline">🛒 Buka Kasir</a>
    </div>
    @endif
</div>
@endsection