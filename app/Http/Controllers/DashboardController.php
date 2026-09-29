<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $baseQuery = Ticket::query()
            ->where('requester_id', auth()->id());

        $totalTickets = (clone $baseQuery)->count();

        $activeTickets = (clone $baseQuery)
            ->whereHas('status', function ($query) {
                $query->where('is_closed', false);
            })
            ->count();

        $closedTickets = (clone $baseQuery)
            ->whereHas('status', function ($query) {
                $query->where('is_closed', true);
            })
            ->count();

        $tickets = (clone $baseQuery)
            ->with([
                'status',
                'requestType',
            ])
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'tickets',
            'totalTickets',
            'activeTickets',
            'closedTickets',
        ));
    }
}
