<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $userType = $request->user()->is_admin? 'admin' : 'member';
        return $this->$userType();
    }

    public function admin(){
        return Inertia::render('Dashboard');
    }

    public function member(){
        abort_if(!auth()->user()->member, 403);
        $checkouts = auth()->user()->member->checkouts()->with('book')->paginate(5);
        $reservations = auth()->user()->member->reservations()->with('book')->paginate(5);
        $user = auth()->user()->load('member');
        return Inertia::render('MemberDashboard', compact('checkouts', 'reservations', 'user'));
    }
}
