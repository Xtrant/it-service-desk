<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketApproval;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TicketApprovalController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('handle', $ticket);

        if ($ticket->assigned_to !== $user->id) {
            return back()->with(
                'error',
                'Approval hanya dapat diajukan oleh PIC ticket.'
            );
        }

        $requestType = $ticket->requestType;

        if (!$requestType?->requires_approval) {
            return back()->with(
                'error',
                'Jenis ticket ini tidak memerlukan approval.'
            );
        }

        if ($ticket->status?->code !== 'IN_PROGRESS') {
            return back()->with(
                'error',
                'Approval hanya dapat diajukan ketika ticket sedang ditangani.'
            );
        }

        $validated = $request->validate([
            'approval_type' => [
                'required',
                'string',
                'max:50',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Tentukan approver otomatis
    |--------------------------------------------------------------------------
    |
    | SUPERVISOR pada workflow ticketing ini berarti
    | MIS/IT Supervisor, bukan supervisor department requester.
    |
    */

        if ($requestType->approval_source === 'SUPERVISOR') {
            $approver = \App\Models\User::query()
                ->where('is_active', true)
                ->whereHas('role', function ($query) {
                    $query->where('name', 'mis_supervisor');
                })
                ->orderBy('id')
                ->first();

            if (!$approver) {
                return back()->with(
                    'error',
                    'MIS Supervisor aktif belum tersedia.'
                );
            }
        } else {
            return back()->with(
                'error',
                'Sumber approval ticket tidak valid.'
            );
        }

        $existingPending = $ticket->approvals()
            ->where('status', 'PENDING')
            ->exists();

        if ($existingPending) {
            return back()->with(
                'error',
                'Ticket sudah memiliki approval yang masih menunggu.'
            );
        }

        DB::transaction(function () use (
            $ticket,
            $user,
            $validated,
            $approver
        ) {
            $approval = $ticket->approvals()->create([
                'approver_id' => $approver->id,
                'approval_type' => $validated['approval_type'],
                'status' => 'PENDING',
                'notes' => $validated['notes'] ?? null,
            ]);

            $status = TicketStatus::where(
                'code',
                'WAITING_APPROVAL'
            )->firstOrFail();

            $ticket->update([
                'status_id' => $status->id,
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'approval_requested',
                'description' =>
                'Approval "' .
                    $approval->approval_type .
                    '" diajukan kepada ' .
                    $approver->name .
                    '.',
            ]);
        });

        return back()->with(
            'success',
            'Approval berhasil diajukan kepada ' .
                $approver->name .
                '.'
        );
    }

    public function approve(
        Request $request,
        Ticket $ticket,
        TicketApproval $approval
    ) {
        $user = $request->user();

        Gate::authorize('approve', $ticket);

        if ($approval->ticket_id !== $ticket->id) {
            abort(404);
        }

        if ($approval->approver_id !== $user->id) {
            return back()->with(
                'error',
                'Approval ini bukan ditujukan kepada Anda.'
            );
        }

        if ($approval->status !== 'PENDING') {
            return back()->with(
                'error',
                'Approval sudah diproses.'
            );
        }

        $validated = $request->validate([
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'signature' => [
                'required',
                'string',
            ],
        ]);

        $signatureData = $validated['signature'];

        if (!str_starts_with(
            $signatureData,
            'data:image/png;base64,'
        )) {
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

        $fileName = 'approval-signature-' .
            $approval->id .
            '-' .
            now()->format('YmdHis') .
            '.png';

        $path = $directory . '/' . $fileName;

        DB::transaction(function () use (
            $ticket,
            $approval,
            $user,
            $validated,
            $path,
            $signatureBinary
        ) {
            \Storage::disk('local')->put(
                $path,
                $signatureBinary
            );

            $approval->update([
                'status' => 'APPROVED',
                'notes' => $validated['notes']
                    ?? $approval->notes,
                'signature_path' => $path,
                'approved_at' => now(),
            ]);

            $status = TicketStatus::where(
                'code',
                'IN_PROGRESS'
            )->firstOrFail();

            $ticket->update([
                'status_id' => $status->id,
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'approval_approved',
                'description' =>
                'Approval "' .
                    $approval->approval_type .
                    '" disetujui oleh ' .
                    $user->name .
                    '.',
            ]);
        });

        return back()->with(
            'success',
            'Approval berhasil disetujui.'
        );
    }

    public function reject(
        Request $request,
        Ticket $ticket,
        TicketApproval $approval
    ) {
        $user = $request->user();

        Gate::authorize('approve', $ticket);

        if ($approval->ticket_id !== $ticket->id) {
            abort(404);
        }

        if ($approval->approver_id !== $user->id) {
            return back()->with(
                'error',
                'Approval ini bukan ditujukan kepada Anda.'
            );
        }

        if ($approval->status !== 'PENDING') {
            return back()->with(
                'error',
                'Approval sudah diproses.'
            );
        }

        $validated = $request->validate([
            'notes' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        DB::transaction(function () use (
            $ticket,
            $approval,
            $user,
            $validated
        ) {
            $approval->update([
                'status' => 'REJECTED',
                'notes' => $validated['notes'],
            ]);

            $status = TicketStatus::where(
                'code',
                'IN_PROGRESS'
            )->firstOrFail();

            $ticket->update([
                'status_id' => $status->id,
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'approval_rejected',
                'description' =>
                'Approval "' .
                    $approval->approval_type .
                    '" ditolak oleh ' .
                    $user->name .
                    '.',
            ]);
        });

        return back()->with(
            'success',
            'Approval ditolak dan ticket dikembalikan ke IN_PROGRESS.'
        );
    }
}
