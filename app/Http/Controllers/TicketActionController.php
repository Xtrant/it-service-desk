<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TicketActionController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('handle', $ticket);

        if ($ticket->assigned_to !== $user->id) {
            return back()->with(
                'error',
                'Action hanya dapat dibuat oleh PIC ticket.'
            );
        }

        if ($ticket->status?->code !== 'IN_PROGRESS') {
            return back()->with(
                'error',
                'Action hanya dapat dilakukan pada ticket yang sedang ditangani.'
            );
        }

        $validated = $request->validate([
            'action_type' => [
                'required',
                'string',
                'max:50',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        DB::transaction(function () use (
            $ticket,
            $user,
            $validated
        ) {
            $ticket->actions()->create([
                'action_type' => $validated['action_type'],
                'performed_by' => $user->id,
                'description' => $validated['description'] ?? null,
                'performed_at' => now(),
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'action_added',
                'description' => 'Action dilakukan oleh ' . $user->name . '.',
            ]);
        });

        return back()->with(
            'success',
            'Action berhasil disimpan.'
        );
    }
}