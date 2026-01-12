<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Get financial report data
        $reportData = $this->getFinancialReport($period, $startDate, $endDate);

        return view('financial.index', $reportData);
    }

    private function getFinancialReport($period, $startDate, $endDate)
    {
        $start = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

        // Base query
        $baseQuery = Transaction::whereBetween('created_at', [$start, $end]);

        // Calculate financial stats
        $totalRevenue = $baseQuery->sum('total');
        $totalCost = $baseQuery->sum('total_cost');
        $totalProfit = $baseQuery->sum('total_profit');

        // Get detailed data based on period
        $detailData = $this->getDetailedFinancialData($period, $start, $end);

        // Get chart data
        $chartData = $this->getFinancialChartData($period, $start, $end);

        return [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'totalProfit' => $totalProfit,
            'detailData' => $detailData,
            'chartLabels' => $chartData['labels'],
            'chartRevenue' => $chartData['revenue'],
            'chartExpense' => $chartData['expense'],
        ];
    }

    private function getDetailedFinancialData($period, $start, $end)
    {
        if ($period === 'daily') {
            return Transaction::whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, SUM(total_cost) as expense, SUM(total_profit) as profit')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn($item) => [
                    'period' => $item->date,
                    'revenue' => (float) $item->revenue,
                    'expense' => (float) $item->expense,
                    'profit' => (float) $item->profit,
                    'margin' => $item->revenue > 0 ? round(($item->profit / $item->revenue) * 100, 2) : 0,
                ])
                ->toArray();
        }

        return [];
    }

    private function getFinancialChartData($period, $start, $end)
    {
        $labels = [];
        $revenue = [];
        $expense = [];

        if ($period === 'daily') {
            $currentDate = $start->copy();
            while ($currentDate <= $end) {
                $dayStart = $currentDate->copy()->startOfDay();
                $dayEnd = $currentDate->copy()->endOfDay();
                
                $dayData = Transaction::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->selectRaw('SUM(total) as revenue, SUM(total_cost) as expense')
                    ->first();
                
                $labels[] = $currentDate->format('d');
                $revenue[] = (float) ($dayData->revenue ?? 0);
                $expense[] = (float) ($dayData->expense ?? 0);
                $currentDate->addDay();
            }
        } elseif ($period === 'weekly') {
            $currentDate = $start->copy()->startOfWeek();
            while ($currentDate <= $end) {
                $weekStart = $currentDate->copy()->startOfWeek();
                $weekEnd = $currentDate->copy()->endOfWeek();
                
                $weekData = Transaction::whereBetween('created_at', [$weekStart, $weekEnd])
                    ->selectRaw('SUM(total) as revenue, SUM(total_cost) as expense')
                    ->first();
                
                $labels[] = 'W' . $currentDate->format('W');
                $revenue[] = (float) ($weekData->revenue ?? 0);
                $expense[] = (float) ($weekData->expense ?? 0);
                $currentDate->addWeek();
            }
        } elseif ($period === 'monthly') {
            $currentDate = $start->copy()->startOfMonth();
            while ($currentDate <= $end) {
                $monthStart = $currentDate->copy()->startOfMonth();
                $monthEnd = $currentDate->copy()->endOfMonth();
                
                $monthData = Transaction::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->selectRaw('SUM(total) as revenue, SUM(total_cost) as expense')
                    ->first();
                
                $labels[] = $currentDate->format('M');
                $revenue[] = (float) ($monthData->revenue ?? 0);
                $expense[] = (float) ($monthData->expense ?? 0);
                $currentDate->addMonth();
            }
        } elseif ($period === 'yearly') {
            $currentDate = $start->copy()->startOfYear();
            while ($currentDate <= $end) {
                $yearStart = $currentDate->copy()->startOfYear();
                $yearEnd = $currentDate->copy()->endOfYear();
                
                $yearData = Transaction::whereBetween('created_at', [$yearStart, $yearEnd])
                    ->selectRaw('SUM(total) as revenue, SUM(total_cost) as expense')
                    ->first();
                
                $labels[] = $currentDate->format('Y');
                $revenue[] = (float) ($yearData->revenue ?? 0);
                $expense[] = (float) ($yearData->expense ?? 0);
                $currentDate->addYear();
            }
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'expense' => $expense,
        ];
    }
}
