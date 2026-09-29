<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketInspectionController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('handle', $ticket);

        if ($ticket->assigned_to !== $user->id) {
            return back()->with(
                'error',
                'Inspection hanya dapat dibuat oleh PIC ticket.'
            );
        }

        if ($ticket->status?->code !== 'IN_PROGRESS') {
            return back()->with(
                'error',
                'Inspection hanya dapat dilakukan pada ticket yang sedang ditangani.'
            );
        }

        $validated = $request->validate([
            'result' => [
                'required',
                'string',
                'max:100',
            ],

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

        $fileName =
            'inspection-signature-' .
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
            $validated,
            $path
        ) {
            $ticket->inspections()->create([
                'inspector_id' => $user->id,
                'result' => $validated['result'],
                'notes' => $validated['notes'] ?? null,
                'signature_path' => $path,
                'inspected_at' => now(),
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'inspection_added',
                'description' =>
                    'Inspection dilakukan oleh ' .
                    $user->name .
                    '.',
            ]);
        });

        return back()->with(
            'success',
            'Inspection berhasil disimpan.'
        );
    }
}