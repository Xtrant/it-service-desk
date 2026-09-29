<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'employee',
            'it_staff',
            'mis_supervisor',
            'admin',
        ]);
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole([
            'it_staff',
            'mis_supervisor',
            'admin',
        ])) {
            return true;
        }

        return $ticket->requester_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole([
            'it_staff',
            'mis_supervisor',
            'admin',
        ])) {
            return true;
        }

        return $ticket->requester_id === $user->id;
    }

    public function comment(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole([
            'it_staff',
            'mis_supervisor',
            'admin',
        ])) {
            return true;
        }

        return $ticket->requester_id === $user->id;
    }

    public function handle(User $user, Ticket $ticket): bool
    {
        return $user->hasRole([
            'it_staff',
            'admin',
        ]);
    }

    public function approve(
        User $user,
        Ticket $ticket
    ): bool {
        return $ticket->approvals()
            ->where('approver_id', $user->id)
            ->where('status', 'PENDING')
            ->exists();
    }

    public function resolve(User $user, Ticket $ticket): bool
    {
        return $user->hasRole([
            'it_staff',
            'mis_supervisor',
            'admin',
        ])
            && $ticket->assigned_to === $user->id;
    }

    public function close(User $user, Ticket $ticket): bool
    {
        return $ticket->requester_id === $user->id;
    }

    public function reopen(User $user, Ticket $ticket): bool
    {
        return $ticket->requester_id === $user->id
            || $user->hasRole('admin');
    }
}