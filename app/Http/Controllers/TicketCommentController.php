<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TicketCommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('view', $ticket);

        $validated = $request->validate([
            'comment' => [
                'required',
                'string',
                'max:5000',
            ],

            'is_internal' => [
                'nullable',
                'boolean',
            ],
        ]);

        $isInternal = (bool) (
            $validated['is_internal'] ?? false
        );

        if ($isInternal && $user->hasRole('employee')) {
            abort(403);
        }

        DB::transaction(function () use (
            $ticket,
            $user,
            $validated,
            $isInternal
        ) {
            $ticket->comments()->create([
                'user_id' => $user->id,
                'comment' => $validated['comment'],
                'is_internal' => $isInternal,
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => $isInternal
                    ? 'internal_comment_added'
                    : 'comment_added',

                'description' => $isInternal
                    ? 'Internal note ditambahkan.'
                    : 'Comment ditambahkan.',
            ]);
        });

        return back()->with(
            'success',
            'Comment berhasil ditambahkan.'
        );
    }
}