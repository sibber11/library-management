<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\CheckOut;
use App\Models\Member;
use App\Models\Reservation;
use Carbon\Carbon;
use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $userType = $request->user()->is_admin ? 'admin' : 'member';
        return $this->$userType();
    }

    public function admin()
    {
        $lineChart = $this->getLineChart();
        $totalBooks = Book::count();
        $availableBooks = Book::available()->count();
        $issuedBooks = CheckOut::checkedout()->count();
        $reservations = Reservation::whereIn('status', ['pending', 'reserved'])->count();

        $totalMembers = Member::count();
        $activeMembers = Member::active()->count();
        $expiredMembers = Member::expired()->count();

        $overdueBooks = CheckOut::with(['member.user', 'book'])->checkedout()->whereDate('due_date', '<', Carbon::today())->paginate();
        // throw new Exception($issuedBooks);
        return Inertia::render('Dashboard', [
            'lineChart' => $lineChart,
            'inventory' => [
                'totalBooks' => $totalBooks,
                'availableBooks' => $availableBooks,
                'issuedBooks' => $issuedBooks,
                'reservations' => $reservations
            ],
            'members' => [
                'totalMembers' => $totalMembers,
                'activeMembers' => $activeMembers,
                'expiredMembers' => $expiredMembers
            ],
            'overdueBooks' => $overdueBooks
        ]);
    }

    public function member()
    {
        abort_if(!auth()->user()->member, 403);
        $checkouts = auth()->user()->member->checkouts()->with('book')->paginate(5);
        $reservations = auth()->user()->member->reservations()->with('book')->paginate(5);
        $user = auth()->user()->load('member');
        return Inertia::render('MemberDashboard', compact('checkouts', 'reservations', 'user'));
    }

    public function getLineChart()
    {
        $previousWeek = Carbon::now()->subWeek();
        $checkoutsPerDay = CheckOut::query()
            ->selectRaw('DATE(check_out_date) as date, COUNT(*) as count')
            ->whereNotNull('check_in_date')
            ->where('check_out_date', '>=', $previousWeek)
            ->groupBy('date')
            ->get();
        $reservationsPerDay = Reservation::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $previousWeek)
            ->groupBy('date')
            ->get();
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::yesterday()->subDays($i);
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $date->format('l');
            if (!$checkoutsPerDay->contains('date', $formattedDate)) {
                $checkoutsPerDay->push(['date' => $formattedDate, 'count' => 0]);
            }
            if (!$reservationsPerDay->contains('date', $formattedDate)) {
                $reservationsPerDay->push(['date' => $formattedDate, 'count' => 0]);
            }
        }
        // data for the line chart
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Checkouts per day',
                    'borderColor' => 'rgb(249, 115, 22)',
                    'data' => $checkoutsPerDay->sortBy('date')->pluck('count'),
                    'cubicInterpolationMode' => 'monotone'
                ],
                [
                    'label' => 'Reservations per day',
                    'borderColor' => 'rgb(236, 72, 153)',
                    'data' => $reservationsPerDay->sortBy('date')->pluck('count'),
                    'cubicInterpolationMode' => 'monotone'
                ]
            ]
        ];
    }
}
