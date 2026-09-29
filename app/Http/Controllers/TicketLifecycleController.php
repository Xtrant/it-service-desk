<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketLifecycleController extends Controller
{
    public function resolve(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('resolve', $ticket);

        if ($ticket->status?->code !== 'IN_PROGRESS') {
            return back()->with(
                'error',
                'Ticket hanya dapat diselesaikan dari status IN_PROGRESS.'
            );
        }

        DB::transaction(function () use ($ticket, $user) {
            $status = TicketStatus::where(
                'code',
                'RESOLVED'
            )->firstOrFail();

            $ticket->update([
                'status_id' => $status->id,
                'resolved_at' => now(),
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'resolved',
                'old_value' => 'IN_PROGRESS',
                'new_value' => 'RESOLVED',
                'description' => 'Ticket ditandai selesai oleh ' . $user->name . '.',
            ]);
        });

        return back()->with(
            'success',
            'Ticket berhasil ditandai sebagai selesai.'
        );
    }

    public function close(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('view', $ticket);

        if ($ticket->requester_id !== $user->id) {
            return back()->with(
                'error',
                'Hanya requester yang dapat mengonfirmasi penyelesaian ticket.'
            );
        }

        if ($ticket->status?->code !== 'RESOLVED') {
            return back()->with(
                'error',
                'Ticket harus berstatus RESOLVED terlebih dahulu.'
            );
        }

        $validated = $request->validate([
            'signature' => [
                'required',
                'string',
            ],
        ]);

        $signatureData = $validated['signature'];

        if (
            !str_starts_with(
                $signatureData,
                'data:image/png;base64,'
            )
        ) {
            return back()
                ->withErrors([
                    'signature' => 'Format signature tidak valid.',
                ])
                ->withInput();
        }

        $signatureBase64 = substr(
            $signatureData,
            strlen('data:image/png;base64,')
        );

        $signatureBinary = base64_decode(
            $signatureBase64,
            true
        );

        if ($signatureBinary === false) {
            return back()
                ->withErrors([
                    'signature' => 'Signature tidak valid.',
                ])
                ->withInput();
        }

        if (strlen($signatureBinary) > 2 * 1024 * 1024) {
            return back()
                ->withErrors([
                    'signature' => 'Ukuran signature terlalu besar.',
                ])
                ->withInput();
        }

        $directory = 'tickets/' . $ticket->id;

        $fileName = 'requester-signature-' .
            now()->format('YmdHis') .
            '.png';

        $path = $directory . '/' . $fileName;

        Storage::disk('private')->put(
            $path,
            $signatureBinary
        );

        DB::transaction(function () use (
            $ticket,
            $user,
            $path
        ) {
            $oldStatus = $ticket->status?->code;

            $status = TicketStatus::where(
                'code',
                'CLOSED'
            )->firstOrFail();

            $ticket->update([
                'status_id' => $status->id,
                'closed_at' => now(),
                'requester_signature_path' => $path,
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'closed',
                'old_value' => $oldStatus,
                'new_value' => 'CLOSED',
                'description' =>
                'Ticket dikonfirmasi selesai oleh ' .
                    $user->name .
                    '.',
            ]);
        });

        return redirect()
            ->route('tickets.show', $ticket)
            ->with(
                'success',
                'Ticket berhasil dikonfirmasi dan ditutup.'
            );
    }

    public function reopen(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('reopen', $ticket);

        if (!in_array($ticket->status?->code, [
            'RESOLVED',
            'CLOSED',
        ], true)) {
            return back()->with(
                'error',
                'Ticket tidak dapat dibuka kembali dari status saat ini.'
            );
        }

        DB::transaction(function () use ($ticket, $user) {
            $oldStatus = $ticket->status?->code;

            $status = TicketStatus::where(
                'code',
                'IN_PROGRESS'
            )->firstOrFail();

            $ticket->update([
                'status_id' => $status->id,
                'resolved_at' => null,
                'closed_at' => null,
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'reopened',
                'old_value' => $oldStatus,
                'new_value' => 'IN_PROGRESS',
                'description' => 'Ticket dibuka kembali oleh ' . $user->name . '.',
            ]);
        });

        return back()->with(
            'success',
            'Ticket berhasil dibuka kembali.'
        );
    }
}
