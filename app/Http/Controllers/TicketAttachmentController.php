<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('comment', $ticket);

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
            ],
        ]);

        $file = $validated['file'];

        $path = $file->store(
            'tickets/' . $ticket->id,
            'private'
        );

        $attachment = $ticket->attachments()->create([
            'user_id' => $user->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $ticket->activities()->create([
            'user_id' => $user->id,
            'activity_type' => 'attachment_added',
            'description' =>
            'Attachment "' .
                $attachment->file_name .
                '" ditambahkan oleh ' .
                $user->name .
                '.',
        ]);

        return back()->with(
            'success',
            'Attachment berhasil ditambahkan.'
        );
    }

    public function destroy(
        Request $request,
        Ticket $ticket,
        $attachment
    ) {
        $user = $request->user();

        Gate::authorize('comment', $ticket);

        $attachment = $ticket->attachments()
            ->findOrFail($attachment);

        Storage::disk('private')
            ->delete($attachment->file_path);

        $fileName = $attachment->file_name;

        $attachment->delete();

        $ticket->activities()->create([
            'user_id' => $user->id,
            'activity_type' => 'attachment_deleted',
            'description' =>
            'Attachment "' .
                $fileName .
                '" dihapus oleh ' .
                $user->name .
                '.',
        ]);

        return back()->with(
            'success',
            'Attachment berhasil dihapus.'
        );
    }

    public function show(
        Request $request,
        Ticket $ticket,
        $attachment
    ) {
        Gate::authorize('view', $ticket);

        $attachment = $ticket->attachments()
            ->findOrFail($attachment);

        if (!Storage::disk('private')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('private')->response(
            $attachment->file_path,
            $attachment->file_name,
            [
                'Content-Type' => $attachment->mime_type
                    ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' .
                    addslashes($attachment->file_name) .
                    '"',
            ]
        );
    }
}
