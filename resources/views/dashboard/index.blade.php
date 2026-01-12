@extends('layouts.main')

@section('title','Dashboard')
@section('header', 'Dashboard Penjualan')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="card-elevated">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang, {{ $user->name ?? 'Tamu' }}! 👋</h2>
                <p class="text-gray-600">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="mt-4 md:mt-0 text-right">
                <p class="text-sm text-gray-500 mb-1">Penjualan Hari Ini</p>
                <p class="text-3xl font-bold text-accent-green">Rp {{ number_format($today, 0, '.', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Penjualan Hari Ini</h3>
                <span class="text-2xl">📅</span>
            </div>
            <p class="text-2xl font-bold text-accent-green">Rp {{ number_format($today, 0, '.', '.') }}</p>
        </div>
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Penjualan Minggu Ini</h3>
                <span class="text-2xl">📊</span>
            </div>
            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($week, 0, '.', '.') }}</p>
        </div>
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Penjualan Bulan Ini</h3>
                <span class="text-2xl">💰</span>
            </div>
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($month, 0, '.', '.') }}</p>
        </div>
    </div>

    <!-- Stock Monitoring -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card-elevated">
            <h3 class="font-bold text-lg mb-4">📦 Monitoring Stok</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <span class="font-medium text-gray-700">Stok Rendah</span>
                    <span class="text-xl font-bold text-yellow-600">{{ $lowStock }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                    <span class="font-medium text-gray-700">Stok Habis</span>
                    <span class="text-xl font-bold text-red-600">{{ $outStock }}</span>
                </div>
            </div>
        </div>

        <div class="card-elevated">
            <h3 class="font-bold text-lg mb-4">📈 Penjualan Bulanan</h3>
            <canvas id="chart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const data = {
        labels: {!! json_encode(array_keys($monthly->toArray() ?? [])) !!},
        datasets: [{
            label: 'Keuntungan',
            data: {!! json_encode(array_values($monthly->toArray() ?? [])) !!},
            borderColor: '#6dbf7a',
            backgroundColor: 'rgba(109,191,122,0.12)',
            tension: 0.4
        }]
    };

    const ctx = document.getElementById('chart');
    if (ctx) {
        new Chart(ctx, { type: 'line', data });
    }
</script>
@endpush
@endsection