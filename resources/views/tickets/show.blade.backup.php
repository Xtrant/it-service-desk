<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        {{ $ticket->ticket_number }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    {{-- =========================================================
         FLASH MESSAGE
    ========================================================== --}}

    @if (session('success'))
    <div>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div>
        {{ session('error') }}
    </div>
    @endif


    {{-- =========================================================
         VALIDATION ERROR
    ========================================================== --}}

    @if ($errors->any())
    <div>
        <strong>Error:</strong>

        <ul>
            @foreach ($errors->all() as $error)
            <li>
                {{ $error }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <a href="{{ route('tickets.index') }}">
        ← Kembali
    </a>

    <h1>
        {{ $ticket->ticket_number }}
    </h1>

    <h2>
        {{ $ticket->title }}
    </h2>

    <p>
        Status:
        <strong>
            {{ $ticket->status?->name }}
        </strong>
    </p>


    {{-- =========================================================
         PRIMARY ACTION
    ========================================================== --}}

    <div>

        {{-- Ambil Ticket --}}
        @if (
        auth()->user()->hasRole([
        'it_staff',
        'mis_supervisor',
        'admin',
        ])
        &&
        $ticket->assigned_to === null
        &&
        $ticket->status?->code === 'OPEN'
        )

        <form
            method="POST"
            action="{{ route('tickets.take', $ticket) }}">
            @csrf

            <button type="submit">
                Ambil Ticket
            </button>
        </form>

        @endif


        {{-- Mulai Penanganan --}}
        @if (
        $ticket->assigned_to === auth()->id()
        &&
        $ticket->status?->code === 'OPEN'
        )

        <form
            method="POST"
            action="{{ route('tickets.start', $ticket) }}">
            @csrf

            <button type="submit">
                Mulai Penanganan
            </button>
        </form>

        @endif


        {{-- Tandai Selesai --}}
        @if (
        $ticket->assigned_to === auth()->id()
        &&
        $ticket->status?->code === 'IN_PROGRESS'
        )

        <form
            method="POST"
            action="{{ route('tickets.resolve', $ticket) }}">
            @csrf

            <button type="submit">
                Tandai Selesai
            </button>
        </form>

        @endif


        @if (
        $ticket->requester_id === auth()->id()
        &&
        $ticket->status?->code === 'RESOLVED'
        )

        <h3>
            Konfirmasi Penyelesaian
        </h3>

        <p>
            Ticket ini telah diselesaikan oleh IT.
            Silakan periksa hasil penyelesaian sebelum
            melakukan konfirmasi dan penutupan ticket.
        </p>

        <form
            method="POST"
            action="{{ route('tickets.close', $ticket) }}"
            id="close-ticket-form">
            @csrf

            <div>

                <label>
                    Tanda Tangan
                </label>

                <br>

                <canvas
                    id="signature-pad"
                    width="600"
                    height="250"
                    style="border: 1px solid #000; touch-action: none;"></canvas>

            </div>

            <input
                type="hidden"
                name="signature"
                id="signature">

            <br>

            <button
                type="button"
                id="clear-signature">
                Bersihkan
            </button>

            <button
                type="submit"
                id="submit-close-ticket">
                Konfirmasi & Tutup Ticket
            </button>

        </form>

        @endif


        {{-- Reopen --}}
        @if (
        in_array(
        $ticket->status?->code,
        [
        'RESOLVED',
        'CLOSED',
        ],
        true
        )
        &&
        (
        $ticket->requester_id === auth()->id()
        ||
        auth()->user()->hasRole([
        'it_staff',
        'mis_supervisor',
        'admin',
        ])
        )
        )

        <form
            method="POST"
            action="{{ route('tickets.reopen', $ticket) }}">
            @csrf

            <button type="submit">
                Buka Kembali
            </button>
        </form>

        @endif

    </div>


    <hr>


    {{-- =========================================================
         TICKET INFORMATION
    ========================================================== --}}

    <h3>
        Informasi Ticket
    </h3>

    <p>
        <strong>Priority:</strong>
        {{ $ticket->priority }}
    </p>

    <p>
        <strong>Requester:</strong>
        {{ $ticket->requester?->name }}
    </p>

    <p>
        <strong>Department:</strong>
        {{ $ticket->department?->name }}
    </p>

    <p>
        <strong>Request Type:</strong>
        {{ $ticket->requestType?->name }}
    </p>

    <p>
        <strong>PIC:</strong>

        @if ($ticket->assignee)
        {{ $ticket->assignee->name }}
        @else
        Belum ada PIC
        @endif
    </p>

    @if ($ticket->asset)
    <p>
        <strong>Asset:</strong>
        {{ $ticket->asset->asset_code }}
        -
        {{ $ticket->asset->name }}
    </p>
    @endif

    @if ($ticket->needed_at)
    <p>
        <strong>Dibutuhkan:</strong>
        {{ $ticket->needed_at->format('d M Y H:i') }}
    </p>
    @endif

    @if ($ticket->submitted_at)
    <p>
        <strong>Submitted:</strong>
        {{ $ticket->submitted_at->format('d M Y H:i') }}
    </p>
    @endif

    @if ($ticket->resolved_at)
    <p>
        <strong>Resolved:</strong>
        {{ $ticket->resolved_at->format('d M Y H:i') }}
    </p>
    @endif

    @if ($ticket->closed_at)
    <p>
        <strong>Closed:</strong>
        {{ $ticket->closed_at->format('d M Y H:i') }}
    </p>
    @endif

    @if ($ticket->requester_signature_path)

    <p>
        <strong>
            Requester Signature:
        </strong>
    </p>

    <p>
        {{ $ticket->requester?->name }}
    </p>

    <img
        src="{{ route(
        'tickets.signatures.requester',
        $ticket
    ) }}"
        alt="Requester Signature"
        style="
            max-width: 400px;
            max-height: 180px;
            border: 1px solid #ccc;
        ">

    @endif


    <hr>


    {{-- =========================================================
         DESCRIPTION
    ========================================================== --}}

    <h3>
        Description
    </h3>

    <p>
        {!! nl2br(e($ticket->description)) !!}
    </p>


    <hr>


    {{-- =========================================================
         CONVERSATION
    ========================================================== --}}

    <h3>
        Conversation
    </h3>

    @forelse ($ticket->comments as $comment)

    @if (
    !$comment->is_internal
    ||
    auth()->user()->hasRole([
    'it_staff',
    'mis_supervisor',
    'admin',
    ])
    )

    <div>

        <strong>
            {{ $comment->user?->name }}
        </strong>

        @if ($comment->is_internal)
        <span>
            [Internal Note]
        </span>
        @endif

        <small>
            {{ $comment->created_at?->format('d M Y H:i') }}
        </small>

        <p>
            {!! nl2br(e($comment->comment)) !!}
        </p>

    </div>

    <hr>

    @endif

    @empty

    <p>
        Belum ada comment.
    </p>

    @endforelse


    <h3>
        Tambah Comment
    </h3>

    <form
        method="POST"
        action="{{ route('tickets.comments.store', $ticket) }}">
        @csrf

        <div>

            <label for="comment">
                Comment
            </label>

            <br>

            <textarea
                id="comment"
                name="comment"
                rows="5"
                cols="60"
                maxlength="5000"
                required
                placeholder="Tulis comment...">{{ old('comment') }}</textarea>

        </div>


        @if (
        auth()->user()->hasRole([
        'it_staff',
        'mis_supervisor',
        'admin',
        ])
        )

        <div>

            <label>
                <input
                    type="checkbox"
                    name="is_internal"
                    value="1">

                Internal Note
            </label>

        </div>

        @endif


        <button type="submit">
            Tambah Comment
        </button>

    </form>


    <hr>


    {{-- =========================================================
         ACTIVITY TIMELINE
    ========================================================== --}}

    <h3>
        Activity Timeline
    </h3>

    @forelse (
    $ticket->activities->sortBy('created_at')
    as $activity
    )

    @php

    $activityLabel = match ($activity->activity_type) {

    'created'
    => 'Ticket dibuat',

    'assigned'
    => 'PIC ditetapkan',

    'status_changed'
    => 'Status berubah',

    'comment_added'
    => 'Comment ditambahkan',

    'internal_comment_added'
    => 'Internal note ditambahkan',

    'inspection_added'
    => 'Inspection dilakukan',

    'action_added'
    => 'Action dilakukan',

    'approval_requested'
    => 'Approval diajukan',

    'approval_approved'
    => 'Approval disetujui',

    'approval_rejected'
    => 'Approval ditolak',

    'resolved'
    => 'Ticket diselesaikan',

    'closed'
    => 'Ticket ditutup',

    'reopened'
    => 'Ticket dibuka kembali',

    default
    => ucfirst(
    str_replace(
    '_',
    ' ',
    $activity->activity_type
    )
    ),

    'attachment_added'
    => 'Attachment ditambahkan',

    'attachment_deleted'
    => 'Attachment dihapus',
    };

    @endphp


    <div>

        <strong>
            {{ $activityLabel }}
        </strong>

        <small>
            {{ $activity->created_at?->format('d M Y H:i') }}
        </small>

        <p>
            {{ $activity->description }}
        </p>


        {{-- Assigned --}}
        @if ($activity->activity_type === 'assigned')

        <p>
            <strong>
                PIC:
            </strong>

            {{ $activity->user?->name }}
        </p>

        {{-- Status Change --}}
        @elseif ($activity->activity_type === 'status_changed')

        @if ($activity->old_value !== null)

        <p>
            <strong>
                Dari:
            </strong>

            {{ str_replace(
                            '_',
                            ' ',
                            $activity->old_value
                        ) }}
        </p>

        @endif

        @if ($activity->new_value !== null)

        <p>
            <strong>
                Menjadi:
            </strong>

            {{ str_replace(
                            '_',
                            ' ',
                            $activity->new_value
                        ) }}
        </p>

        @endif

        @endif


        <small>
            Oleh:
            {{ $activity->user?->name }}
        </small>

    </div>

    <hr>

    @empty

    <p>
        Belum ada activity.
    </p>

    @endforelse


    <hr>


    {{-- =========================================================
         INSPECTION
    ========================================================== --}}

    <h3>
        Inspection
    </h3>

    @forelse (
    $ticket->inspections->sortByDesc('inspected_at')
    as $inspection
    )

    <div>

        <strong>
            {{ $inspection->inspector?->name }}
        </strong>

        <small>
            {{ $inspection->inspected_at?->format('d M Y H:i') }}
        </small>

        <p>
            <strong>
                Result:
            </strong>

            {{ $inspection->result }}
        </p>

        @if ($inspection->notes)

        <p>
            <strong>
                Notes:
            </strong>

            <br>

            {!! nl2br(e($inspection->notes)) !!}
        </p>

        @endif

        @if ($inspection->signature_path)

        <p>
            <strong>
                Inspector Signature:
            </strong>
        </p>

        <img
            src="{{ route(
        'tickets.signatures.inspection',
        [
            $ticket,
            $inspection
        ]
    ) }}"
            alt="Inspector Signature"
            style="
        max-width: 400px;
        max-height: 180px;
        border: 1px solid #ccc;
    ">

        @endif

    </div>

    <hr>

    @empty

    <p>
        Belum ada inspection.
    </p>

    @endforelse


    @if (
    auth()->user()->hasRole([
    'it_staff',
    'mis_supervisor',
    'admin',
    ])
    &&
    $ticket->assigned_to === auth()->id()
    &&
    $ticket->status?->code === 'IN_PROGRESS'
    )

    <h3>
        Tambah Inspection
    </h3>

    <form
        method="POST"
        action="{{ route(
        'tickets.inspections.store',
        $ticket
    ) }}"
        id="inspection-form">
        @csrf

        <div>

            <label for="result">
                Hasil Pemeriksaan
            </label>

            <br>

            <input
                type="text"
                id="result"
                name="result"
                maxlength="100"
                required>

        </div>

        <div>

            <label for="inspection_notes">
                Catatan Pemeriksaan
            </label>

            <br>

            <textarea
                id="inspection_notes"
                name="notes"
                rows="5"
                cols="60"
                maxlength="5000"></textarea>

        </div>

        <div>

            <label>
                Tanda Tangan Inspector
            </label>

            <br>

            <canvas
                id="inspection-signature-pad"
                width="600"
                height="250"
                style="
                border: 1px solid #000;
                touch-action: none;
            "></canvas>

        </div>

        <input
            type="hidden"
            name="signature"
            id="inspection-signature">

        <br>

        <button
            type="button"
            id="clear-inspection-signature">
            Bersihkan
        </button>

        <button
            type="submit"
            id="submit-inspection">
            Simpan Inspection
        </button>

    </form>

    @endif


    <hr>


    {{-- =========================================================
         ACTION HISTORY
    ========================================================== --}}

    <h3>
        Action History
    </h3>

    @forelse (
    $ticket->actions->sortByDesc('performed_at')
    as $action
    )

    <div>

        <strong>
            {{ $action->action_type }}
        </strong>

        <small>
            {{ $action->performed_at?->format('d M Y H:i') }}
        </small>

        @if ($action->description)

        <p>
            {!! nl2br(e($action->description)) !!}
        </p>

        @endif

        <small>
            Dilakukan oleh:
            {{ $action->performer?->name }}
        </small>

    </div>

    <hr>

    @empty

    <p>
        Belum ada action.
    </p>

    @endforelse


    {{-- Tambah Action --}}

    @if (
    auth()->user()->hasRole([
    'it_staff',
    'mis_supervisor',
    'admin',
    ])
    &&
    $ticket->assigned_to === auth()->id()
    &&
    $ticket->status?->code === 'IN_PROGRESS'
    )

    <h3>
        Tambah Action
    </h3>

    <form
        method="POST"
        action="{{ route(
                'tickets.actions.store',
                $ticket
            ) }}">
        @csrf

        <div>

            <label for="action_type">
                Jenis Tindakan
            </label>

            <br>

            <input
                type="text"
                id="action_type"
                name="action_type"
                maxlength="50"
                required
                placeholder="Contoh: Troubleshooting">

        </div>

        <div>

            <label for="action_description">
                Deskripsi Tindakan
            </label>

            <br>

            <textarea
                id="action_description"
                name="description"
                rows="5"
                cols="60"
                maxlength="5000"
                placeholder="Jelaskan tindakan yang dilakukan..."></textarea>

        </div>

        <button type="submit">
            Simpan Action
        </button>

    </form>

    @endif


    <hr>

    {{-- =========================================================
     ATTACHMENTS
========================================================== --}}

    <h3>
        Attachments
    </h3>

    @forelse (
    $ticket->attachments->sortByDesc('created_at')
    as $attachment
    )

    <div>

        <strong>
            {{ $attachment->file_name }}
        </strong>

        <p>
            Ukuran:
            {{ $attachment->file_size
                ? number_format(
                    $attachment->file_size / 1024,
                    1
                ) . ' KB'
                : '-'
            }}
        </p>

        <p>
            Upload oleh:
            {{ $attachment->user?->name }}
        </p>

        <p>
            {{ $attachment->created_at?->format(
                'd M Y H:i'
            ) }}
        </p>

        <a
            href="{{ route(
        'tickets.attachments.show',
        [
            $ticket,
            $attachment
        ]
    ) }}"
            target="_blank">
            Lihat / Download
        </a>


        @if (
        auth()->user()->hasRole([
        'it_staff',
        'mis_supervisor',
        'admin',
        ])
        ||
        $attachment->user_id === auth()->id()
        )

        <form
            method="POST"
            action="{{ route(
                    'tickets.attachments.destroy',
                    [
                        $ticket,
                        $attachment
                    ]
                ) }}">

            @csrf
            @method('DELETE')

            <button type="submit">
                Hapus
            </button>

        </form>

        @endif

    </div>

    <hr>

    @empty

    <p>
        Belum ada attachment.
    </p>

    @endforelse


    {{-- =========================================================
            ADD ATTACHMENT
    ========================================================== --}}

    <h3>
        Tambah Attachment
    </h3>

    @if (
    auth()->user()->hasRole([
    'employee',
    'it_staff',
    'mis_supervisor',
    'admin',
    ])
    )

    <form
        method="POST"
        action="{{ route(
            'tickets.attachments.store',
            $ticket
        ) }}"
        enctype="multipart/form-data">

        @csrf

        <div>

            <label for="file">
                File
            </label>

            <br>

            <input
                type="file"
                id="file"
                name="file"
                required>

        </div>

        <p>
            Maksimal 10 MB.
            Format: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX.
        </p>

        <button type="submit">
            Upload Attachment
        </button>

    </form>

    @endif


    {{-- =========================================================
         APPROVAL
    ========================================================== --}}

    <h3>
        Approval
    </h3>

    @forelse (
    $ticket->approvals->sortByDesc('created_at')
    as $approval
    )

    <div>

        <strong>
            {{ $approval->approval_type }}
        </strong>

        <p>
            Approver:
            {{ $approval->approver?->name }}
        </p>

        <p>
            Status:
            <strong>
                {{ $approval->status }}
            </strong>
        </p>

        @if ($approval->notes)

        <p>
            <strong>
                Notes:
            </strong>

            <br>

            {!! nl2br(e($approval->notes)) !!}
        </p>

        @endif

        @if ($approval->approved_at)

        <p>
            Diproses:
            {{ $approval->approved_at->format('d M Y H:i') }}
        </p>

        @endif

        @if ($approval->signature_path)

        <p>
            <strong>
                Approval Signature:
            </strong>
        </p>

        <p>
            {{ $approval->approver?->name }}
        </p>

        <img
            src="{{ route(
        'tickets.signatures.approval',
        [
            $ticket,
            $approval
        ]
    ) }}"
            alt="Approval Signature"
            style="
            max-width: 400px;
            max-height: 180px;
            border: 1px solid #ccc;
        ">

        @endif


        {{-- Approve / Reject --}}

        @if (
        $approval->status === 'PENDING' &&
        $approval->approver_id === auth()->id()
        )

        <div>
            <h4>Approval Action</h4>

            {{-- APPROVE --}}
            <form
                method="POST"
                action="{{ route(
                'tickets.approvals.approve',
                [$ticket, $approval]
            ) }}"
                id="approval-form-{{ $approval->id }}">
                @csrf

                <div>

                    <label for="approval-notes-{{ $approval->id }}">
                        Catatan Approval
                    </label>

                    <br>

                    <textarea
                        id="approval-notes-{{ $approval->id }}"
                        name="notes"
                        rows="3"
                        cols="60"
                        maxlength="5000"
                        placeholder="Catatan approval...">{{ old('notes') }}</textarea>

                </div>

                <br>

                <div>

                    <label>
                        Tanda Tangan Approval
                    </label>

                    <br>

                    <canvas
                        id="approval-signature-pad-{{ $approval->id }}"
                        width="600"
                        height="250"
                        style="
                        border: 1px solid #000;
                        touch-action: none;
                    "></canvas>

                </div>

                <input
                    type="hidden"
                    name="signature"
                    id="approval-signature-{{ $approval->id }}">

                <br>

                <button
                    type="button"
                    onclick="clearApprovalSignature({{ $approval->id }})">
                    Bersihkan
                </button>

                <button
                    type="submit"
                    onclick="return submitApprovalSignature({{ $approval->id }})">
                    Approve
                </button>

            </form>

            <br>

            {{-- REJECT --}}
            <form
                method="POST"
                action="{{ route(
                'tickets.approvals.reject',
                [$ticket, $approval]
            ) }}">
                @csrf

                <textarea
                    name="notes"
                    rows="3"
                    cols="60"
                    maxlength="5000"
                    required
                    placeholder="Alasan penolakan..."></textarea>

                <br>

                <button type="submit">
                    Reject
                </button>

            </form>

        </div>

        @endif

    </div>

    <hr>

    @empty

    <p>
        Belum ada approval.
    </p>

    @endforelse


    {{-- =========================================================
     REQUEST APPROVAL
========================================================== --}}

    @if (
    auth()->user()->hasRole([
    'it_staff',
    'mis_supervisor',
    'admin',
    ])
    &&
    $ticket->assigned_to === auth()->id()
    &&
    $ticket->status?->code === 'IN_PROGRESS'
    &&
    $ticket->requestType?->requires_approval
    )

    @php
    $hasPendingApproval = $ticket->approvals
    ->where('status', 'PENDING')
    ->isNotEmpty();

    $approver = \App\Models\User::query()
    ->where('is_active', true)
    ->whereHas('role', function ($query) {
    $query->where('name', 'mis_supervisor');
    })
    ->orderBy('id')
    ->first();
    @endphp

    @if (!$hasPendingApproval)

    <h3>
        Request Approval
    </h3>

    @if ($approver)

    <p>
        Approval akan dikirim kepada:
        <strong>
            {{ $approver->name }}
        </strong>
    </p>

    <p>
        Jabatan:
        {{ $approver->position ?? '-' }}
    </p>

    <form
        method="POST"
        action="{{ route(
                    'tickets.approvals.store',
                    $ticket
                ) }}">
        @csrf

        <div>

            <label for="approval_type">
                Approval Type
            </label>

            <br>

            <input
                type="text"
                id="approval_type"
                name="approval_type"
                value="{{ $ticket->requestType?->name }}"
                maxlength="50"
                required>

        </div>

        <div>

            <label for="approval_notes">
                Notes
            </label>

            <br>

            <textarea
                id="approval_notes"
                name="notes"
                rows="4"
                cols="60"
                maxlength="5000"
                placeholder="Catatan untuk approver...">{{ old('notes') }}</textarea>

        </div>



        <button type="submit">
            Request Approval
        </button>

    </form>

    @else

    <p>
        <strong>
            Approval belum dapat diajukan.
        </strong>
    </p>

    <p>
        Requester belum memiliki atasan yang terdaftar
        di sistem.
    </p>

    @endif

    @endif

    @endif

    <script>
        const approvalPads = {};
        const requesterPad = {
            canvas: null,
            ctx: null,
            drawing: false,
            hasSignature: false
        };

        const inspectionPad = {
            canvas: null,
            ctx: null,
            hasSignature: false
        };

        function getCanvasPosition(canvas, event) {
            const rect = canvas.getBoundingClientRect();

            const clientX =
                event.touches?.length ?
                event.touches[0].clientX :
                event.clientX;

            const clientY =
                event.touches?.length ?
                event.touches[0].clientY :
                event.clientY;

            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function setupSignatureCanvas(canvas, onDraw) {
            if (!canvas) {
                return null;
            }

            const ctx = canvas.getContext('2d');

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            let drawing = false;

            function start(event) {
                event.preventDefault();

                drawing = true;

                const position =
                    getCanvasPosition(canvas, event);

                ctx.beginPath();

                ctx.moveTo(
                    position.x,
                    position.y
                );
            }

            function draw(event) {
                if (!drawing) {
                    return;
                }

                event.preventDefault();

                const position =
                    getCanvasPosition(canvas, event);

                ctx.lineTo(
                    position.x,
                    position.y
                );

                ctx.stroke();

                if (onDraw) {
                    onDraw();
                }
            }

            function stop(event) {
                if (event) {
                    event.preventDefault();
                }

                drawing = false;
            }

            canvas.addEventListener(
                'mousedown',
                start
            );

            canvas.addEventListener(
                'mousemove',
                draw
            );

            canvas.addEventListener(
                'mouseup',
                stop
            );

            canvas.addEventListener(
                'mouseleave',
                stop
            );

            canvas.addEventListener(
                'touchstart',
                start, {
                    passive: false
                }
            );

            canvas.addEventListener(
                'touchmove',
                draw, {
                    passive: false
                }
            );

            canvas.addEventListener(
                'touchend',
                stop, {
                    passive: false
                }
            );

            return {
                canvas,
                ctx
            };
        }

        function clearApprovalSignature(approvalId) {
            const pad = approvalPads[approvalId];

            if (!pad) {
                return;
            }

            pad.ctx.clearRect(
                0,
                0,
                pad.canvas.width,
                pad.canvas.height
            );

            const input =
                document.getElementById(
                    'approval-signature-' + approvalId
                );

            if (input) {
                input.value = '';
            }

            pad.hasSignature = false;
        }

        function submitApprovalSignature(approvalId) {
            const pad = approvalPads[approvalId];

            if (!pad) {
                return false;
            }

            if (!pad.hasSignature) {
                alert(
                    'Silakan tanda tangan terlebih dahulu.'
                );

                return false;
            }

            const input =
                document.getElementById(
                    'approval-signature-' + approvalId
                );

            input.value =
                pad.canvas.toDataURL('image/png');

            return true;
        }

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                /*
                 * Approval signatures
                 */
                document
                    .querySelectorAll(
                        '[id^="approval-signature-pad-"]'
                    )
                    .forEach(function(canvas) {

                        const approvalId =
                            canvas.id.replace(
                                'approval-signature-pad-',
                                ''
                            );

                        const pad =
                            setupSignatureCanvas(
                                canvas,
                                function() {
                                    pad.hasSignature = true;
                                }
                            );

                        approvalPads[approvalId] = pad;

                        approvalPads[approvalId]
                            .hasSignature = false;
                    });


                /*
                 * Requester signature
                 */
                const requesterCanvas =
                    document.getElementById(
                        'signature-pad'
                    );

                if (requesterCanvas) {

                    requesterPad.canvas =
                        requesterCanvas;

                    requesterPad.ctx =
                        requesterCanvas.getContext('2d');

                    requesterPad.ctx.lineWidth = 2;
                    requesterPad.ctx.lineCap = 'round';
                    requesterPad.ctx.lineJoin = 'round';

                    let drawing = false;

                    function getPosition(event) {
                        return getCanvasPosition(
                            requesterCanvas,
                            event
                        );
                    }

                    function start(event) {
                        event.preventDefault();

                        drawing = true;

                        const position =
                            getPosition(event);

                        requesterPad.ctx.beginPath();

                        requesterPad.ctx.moveTo(
                            position.x,
                            position.y
                        );
                    }

                    function draw(event) {

                        if (!drawing) {
                            return;
                        }

                        event.preventDefault();

                        const position =
                            getPosition(event);

                        requesterPad.ctx.lineTo(
                            position.x,
                            position.y
                        );

                        requesterPad.ctx.stroke();

                        requesterPad.hasSignature =
                            true;
                    }

                    function stop(event) {
                        if (event) {
                            event.preventDefault();
                        }

                        drawing = false;
                    }

                    requesterCanvas.addEventListener(
                        'mousedown',
                        start
                    );

                    requesterCanvas.addEventListener(
                        'mousemove',
                        draw
                    );

                    requesterCanvas.addEventListener(
                        'mouseup',
                        stop
                    );

                    requesterCanvas.addEventListener(
                        'mouseleave',
                        stop
                    );

                    requesterCanvas.addEventListener(
                        'touchstart',
                        start, {
                            passive: false
                        }
                    );

                    requesterCanvas.addEventListener(
                        'touchmove',
                        draw, {
                            passive: false
                        }
                    );

                    requesterCanvas.addEventListener(
                        'touchend',
                        stop, {
                            passive: false
                        }
                    );

                    // Clear requester signature
                    document
                        .getElementById('clear-signature')
                        .addEventListener(
                            'click',
                            function() {

                                requesterPad.ctx.clearRect(
                                    0,
                                    0,
                                    requesterCanvas.width,
                                    requesterCanvas.height
                                );

                                requesterPad.hasSignature = false;

                                document
                                    .getElementById('signature')
                                    .value = '';
                            }
                        );

                    // Submit requester signature
                    document
                        .getElementById('close-ticket-form')
                        .addEventListener(
                            'submit',
                            function(event) {

                                if (!requesterPad.hasSignature) {

                                    event.preventDefault();

                                    alert(
                                        'Silakan tanda tangan terlebih dahulu.'
                                    );

                                    return;
                                }

                                document
                                    .getElementById('signature')
                                    .value =
                                    requesterCanvas.toDataURL(
                                        'image/png'
                                    );
                            }
                        );
                }

                /*
                 * Inspection signature
                 */
                const inspectionCanvas =
                    document.getElementById(
                        'inspection-signature-pad'
                    );

                if (inspectionCanvas) {

                    inspectionPad.canvas =
                        inspectionCanvas;

                    inspectionPad.ctx =
                        inspectionCanvas.getContext('2d');

                    inspectionPad.ctx.lineWidth = 2;
                    inspectionPad.ctx.lineCap = 'round';
                    inspectionPad.ctx.lineJoin = 'round';

                    let drawing = false;

                    function inspectionPosition(event) {
                        return getCanvasPosition(
                            inspectionCanvas,
                            event
                        );
                    }

                    function inspectionStart(event) {

                        event.preventDefault();

                        drawing = true;

                        const position =
                            inspectionPosition(event);

                        inspectionPad.ctx.beginPath();

                        inspectionPad.ctx.moveTo(
                            position.x,
                            position.y
                        );
                    }

                    function inspectionDraw(event) {

                        if (!drawing) {
                            return;
                        }

                        event.preventDefault();

                        const position =
                            inspectionPosition(event);

                        inspectionPad.ctx.lineTo(
                            position.x,
                            position.y
                        );

                        inspectionPad.ctx.stroke();

                        inspectionPad.hasSignature = true;
                    }

                    function inspectionStop(event) {

                        if (event) {
                            event.preventDefault();
                        }

                        drawing = false;
                    }

                    inspectionCanvas.addEventListener(
                        'mousedown',
                        inspectionStart
                    );

                    inspectionCanvas.addEventListener(
                        'mousemove',
                        inspectionDraw
                    );

                    inspectionCanvas.addEventListener(
                        'mouseup',
                        inspectionStop
                    );

                    inspectionCanvas.addEventListener(
                        'mouseleave',
                        inspectionStop
                    );

                    inspectionCanvas.addEventListener(
                        'touchstart',
                        inspectionStart, {
                            passive: false
                        }
                    );

                    inspectionCanvas.addEventListener(
                        'touchmove',
                        inspectionDraw, {
                            passive: false
                        }
                    );

                    inspectionCanvas.addEventListener(
                        'touchend',
                        inspectionStop, {
                            passive: false
                        }
                    );

                    document
                        .getElementById(
                            'clear-inspection-signature'
                        )
                        .addEventListener(
                            'click',
                            function() {

                                inspectionPad.ctx.clearRect(
                                    0,
                                    0,
                                    inspectionCanvas.width,
                                    inspectionCanvas.height
                                );

                                inspectionPad.hasSignature =
                                    false;

                                document
                                    .getElementById(
                                        'inspection-signature'
                                    )
                                    .value = '';
                            }
                        );

                    document
                        .getElementById(
                            'inspection-form'
                        )
                        .addEventListener(
                            'submit',
                            function(event) {

                                if (!inspectionPad.hasSignature) {

                                    event.preventDefault();

                                    alert(
                                        'Silakan tanda tangan terlebih dahulu.'
                                    );

                                    return;
                                }

                                document
                                    .getElementById(
                                        'inspection-signature'
                                    )
                                    .value =
                                    inspectionCanvas.toDataURL(
                                        'image/png'
                                    );
                            }
                        );
                }
            }
        );
    </script>

</body>

</html>