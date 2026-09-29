<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\View\View;

class ITDashboardController extends Controller
{
    public function index(): View
    {
        $baseQuery = Ticket::query();

        $totalTickets = (clone $baseQuery)->count();

        $openTickets = (clone $baseQuery)
            ->whereNull('assigned_to')
            ->whereHas('status', function ($query) {
                $query->where('code', 'OPEN');
            })
            ->count();

        $inProgressTickets = (clone $baseQuery)
            ->whereHas('status', function ($query) {
                $query->where('code', 'IN_PROGRESS');
            })
            ->count();

        $resolvedTickets = (clone $baseQuery)
            ->whereHas('status', function ($query) {
                $query->where('code', 'RESOLVED');
            })
            ->count();

        $unassignedTickets = (clone $baseQuery)
            ->whereNull('assigned_to')
            ->whereHas('status', function ($query) {
                $query->where('code', 'OPEN');
            })
            ->count();

        $myTickets = (clone $baseQuery)
            ->where('assigned_to', auth()->id())
            ->whereHas('status', function ($query) {
                $query->whereIn('code', [
                    'OPEN',
                    'IN_PROGRESS',
                ]);
            })
            ->count();

        $recentTickets = (clone $baseQuery)
            ->with([
                'requester',
                'requestType',
                'status',
            ])
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        return view('it-dashboard', compact(
            'totalTickets',
            'openTickets',
            'inProgressTickets',
            'resolvedTickets',
            'unassignedTickets',
            'myTickets',
            'recentTickets',
        ));
    }
}
