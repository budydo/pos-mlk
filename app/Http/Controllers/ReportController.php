<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Get sales report data
        $reportData = $this->getSalesReport($period, $startDate, $endDate);

        return view('reports.index', $reportData);
    }

    private function getSalesReport($period, $startDate, $endDate)
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

        // Base query
        $baseQuery = Transaction::whereBetween('created_at', [$start, $end]);

        // Calculate stats
        $totalSales = $baseQuery->sum('total');
        $totalTransactions = $baseQuery->count();
        $avgTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Get detailed data based on period
        $detailData = $this->getDetailedData($period, $start, $end);

        // Get chart data
        $chartData = $this->getChartData($period, $start, $end);

        return [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
            'avgTransaction' => $avgTransaction,
            'detailData' => $detailData,
            'chartLabels' => $chartData['labels'],
            'chartData' => $chartData['data'],
        ];
    }

    private function getDetailedData($period, $start, $end)
    {
        if ($period === 'daily') {
            return Transaction::whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn($item) => [
                    'period' => $item->date,
                    'revenue' => (float) $item->total,
                    'count' => $item->count,
                ])
                ->toArray();
        }

        return [];
    }

    private function getChartData($period, $start, $end)
    {
        $data = [];
        $labels = [];

        if ($period === 'daily') {
            $currentDate = $start->copy();
            while ($currentDate <= $end) {
                $dayStart = $currentDate->copy()->startOfDay();
                $dayEnd = $currentDate->copy()->endOfDay();
                
                $sales = Transaction::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->sum('total');
                
                $labels[] = $currentDate->format('d');
                $data[] = (float) $sales;
                $currentDate->addDay();
            }
        } elseif ($period === 'weekly') {
            $currentDate = $start->copy()->startOfWeek();
            while ($currentDate <= $end) {
                $weekStart = $currentDate->copy()->startOfWeek();
                $weekEnd = $currentDate->copy()->endOfWeek();
                
                $sales = Transaction::whereBetween('created_at', [$weekStart, $weekEnd])
                    ->sum('total');
                
                $labels[] = 'W' . $currentDate->format('W');
                $data[] = (float) $sales;
                $currentDate->addWeek();
            }
        } elseif ($period === 'monthly') {
            $currentDate = $start->copy()->startOfMonth();
            while ($currentDate <= $end) {
                $monthStart = $currentDate->copy()->startOfMonth();
                $monthEnd = $currentDate->copy()->endOfMonth();
                
                $sales = Transaction::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('total');
                
                $labels[] = $currentDate->format('M');
                $data[] = (float) $sales;
                $currentDate->addMonth();
            }
        } elseif ($period === 'yearly') {
            $currentDate = $start->copy()->startOfYear();
            while ($currentDate <= $end) {
                $yearStart = $currentDate->copy()->startOfYear();
                $yearEnd = $currentDate->copy()->endOfYear();
                
                $sales = Transaction::whereBetween('created_at', [$yearStart, $yearEnd])
                    ->sum('total');
                
                $labels[] = $currentDate->format('Y');
                $data[] = (float) $sales;
                $currentDate->addYear();
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
