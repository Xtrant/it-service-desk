<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketInspection;
use App\Models\TicketApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketSignatureController extends Controller
{
    public function requester(
        Request $request,
        Ticket $ticket
    ) {
        Gate::authorize('view', $ticket);

        if (!$ticket->requester_signature_path) {
            abort(404);
        }

        return $this->fileResponse(
            $ticket->requester_signature_path,
            'requester-signature.png'
        );
    }

    public function inspection(
        Request $request,
        Ticket $ticket,
        TicketInspection $inspection
    ) {
        Gate::authorize('view', $ticket);

        if ($inspection->ticket_id !== $ticket->id) {
            abort(404);
        }

        if (!$inspection->signature_path) {
            abort(404);
        }

        return $this->fileResponse(
            $inspection->signature_path,
            'inspection-signature.png'
        );
    }

    public function approval(
        Request $request,
        Ticket $ticket,
        TicketApproval $approval
    ) {
        Gate::authorize('view', $ticket);

        if ($approval->ticket_id !== $ticket->id) {
            abort(404);
        }

        if (!$approval->signature_path) {
            abort(404);
        }

        return $this->fileResponse(
            $approval->signature_path,
            'approval-signature.png'
        );
    }

    private function fileResponse(
        string $path,
        string $fileName
    ) {
        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return Storage::disk('private')->response(
            $path,
            $fileName,
            [
                'Content-Type' =>
                Storage::disk('private')->mimeType($path)
                    ?? 'application/octet-stream',

                'Content-Disposition' =>
                'inline; filename="' .
                    addslashes($fileName) .
                    '"',
            ]
        );
    }
}
