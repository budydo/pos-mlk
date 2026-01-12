@extends('layouts.main')

@section('title','Laporan Keuangan')
@section('header', 'Laporan Keuangan')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="card-elevated">
        <h3 class="font-bold text-lg mb-4">🔍 Filter Laporan Keuangan</h3>
        <form action="{{ route('financial.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Total Pendapatan</h3>
                <span class="text-2xl">📈</span>
            </div>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, '.', '.') }}</p>
        </div>
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Total Pengeluaran</h3>
                <span class="text-2xl">📉</span>
            </div>
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalCost, 0, '.', '.') }}</p>
        </div>
        <div class="card">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm text-gray-600 font-medium">Laba Bersih</h3>
                <span class="text-2xl">💎</span>
            </div>
            <p class="text-2xl font-bold text-accent-green">Rp {{ number_format($totalProfit, 0, '.', '.') }}</p>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-elevated">
            <h3 class="font-bold text-lg mb-4">📊 Grafik Perbandingan Pendapatan vs Pengeluaran</h3>
            <div class="relative h-80">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>

        <div class="card-elevated">
            <h3 class="font-bold text-lg mb-4">💹 Grafik Laba Bersih</h3>
            <div class="relative h-80">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Distribution Chart -->
    <div class="card-elevated">
        <h3 class="font-bold text-lg mb-4">🥧 Distribusi Pendapatan dan Pengeluaran</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="relative h-80">
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="relative h-80">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="card-elevated">
        <h3 class="font-bold text-lg mb-4">📋 Detail Keuangan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm table-striped">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-gray-700">Periode</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Pendapatan</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Pengeluaran</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Laba Bersih</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-700">Margin (%)</th>
                    </tr>
                </thead>
                <tbody id="financialTableBody">
                    @if(count($detailData) > 0)
                        @foreach($detailData as $item)
                        <tr class="hover:bg-emerald-50 transition-colors">
                            <td class="px-4 py-3">{{ $item['period'] }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-green-600">Rp {{ number_format($item['revenue'], 0, '.', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-red-600">Rp {{ number_format($item['expense'], 0, '.', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-accent-green">Rp {{ number_format($item['profit'], 0, '.', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ $item['margin'] }}%</td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="text-center text-gray-500">
                            <td colspan="5" class="px-4 py-3">Tidak ada data untuk periode yang dipilih</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let comparisonChart = null;
    let profitChart = null;
    let revenueChart = null;
    let expenseChart = null;

    function initializeCharts() {
        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartRevenue = {!! json_encode($chartRevenue) !!};
        const chartExpense = {!! json_encode($chartExpense) !!};

        // Update Comparison Chart
        updateComparisonChart(chartLabels, chartRevenue, chartExpense);
        updateProfitChart(chartLabels, chartRevenue, chartExpense);
        updateRevenueChart(chartLabels, chartRevenue);
        updateExpenseChart(chartLabels, chartExpense);
    }

    function updateComparisonChart(labels, revenue, expense) {
        if (comparisonChart) comparisonChart.destroy();
        const ctx1 = document.getElementById('comparisonChart');
        comparisonChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: revenue,
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pengeluaran',
                        data: expense,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
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

    function updateProfitChart(labels, revenue, expense) {
        if (profitChart) profitChart.destroy();
        const ctx2 = document.getElementById('profitChart');
        const profit = revenue.map((v, i) => v - expense[i]);
        profitChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Laba Bersih',
                    data: profit,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(16, 185, 129)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
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

    function updateRevenueChart(labels, revenue) {
        if (revenueChart) revenueChart.destroy();
        const ctx3 = document.getElementById('revenueChart');
        revenueChart = new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: revenue,
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    function updateExpenseChart(labels, expense) {
        if (expenseChart) expenseChart.destroy();
        const ctx4 = document.getElementById('expenseChart');
        expenseChart = new Chart(ctx4, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: expense,
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(217, 119, 6, 0.8)',
                        'rgba(220, 38, 38, 0.8)',
                        'rgba(193, 18, 31, 0.8)',
                        'rgba(153, 27, 27, 0.8)',
                        'rgba(225, 29, 72, 0.8)',
                        'rgba(190, 24, 93, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', initializeCharts);
</script>
@endsection
