@extends('layouts.main')

@section('title','Laporan Penjualan')
@section('header', 'Laporan Penjualan')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="card-elevated">
        <h3 class="font-bold text-lg mb-4">🔍 Filter Laporan</h3>
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <select name="period" id="period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent-green focus:ring-1 focus:ring-accent-green">
                    <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent-green focus:ring-1 focus:ring-accent-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent-green focus:ring-1 focus:ring-accent-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                <button type="submit" class="w-full btn btn-primary">Tampilkan Laporan</button>
            </div>
        </form>
    </div>

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Total Penjualan</h3>
                <span class="text-2xl">💰</span>
            </div>
            <p class="text-2xl font-bold text-accent-green">Rp {{ number_format($totalSales, 0, '.', '.') }}</p>
        </div>
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Jumlah Transaksi</h3>
                <span class="text-2xl">📝</span>
            </div>
            <p class="text-2xl font-bold text-blue-600">{{ $totalTransactions }}</p>
        </div>
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Rata-rata Transaksi</h3>
                <span class="text-2xl">📊</span>
            </div>
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($avgTransaction, 0, '.', '.') }}</p>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card-elevated">
        <h3 class="font-bold text-lg mb-4">📈 Grafik Penjualan</h3>
        <div class="relative h-96">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Sales Detail Section -->
    <div class="card-elevated">
        <h3 class="font-bold text-lg mb-4">📋 Detail Penjualan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm table-striped">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-gray-700">Tanggal</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Total Penjualan</th>
                        <th class="px-4 py-3 text-center font-bold text-gray-700">Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody id="salesTableBody">
                    @if(count($detailData) > 0)
                        @foreach($detailData as $item)
                        <tr class="hover:bg-emerald-50 transition-colors">
                            <td class="px-4 py-3">{{ $item['period'] }}</td>
                            <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item['revenue'], 0, '.', '.') }}</td>
                            <td class="px-4 py-3 text-center">{{ $item['count'] }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="text-center text-gray-500">
                            <td colspan="3" class="px-4 py-3">Tidak ada data untuk periode yang dipilih</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let salesChart = null;

    function initializeChart() {
        const ctx = document.getElementById('salesChart');
        
        if (salesChart) {
            salesChart.destroy();
        }

        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartData = {!! json_encode($chartData) !!};

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: chartData,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(16, 185, 129)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });
    }

    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', initializeChart);
</script>
@endsection
