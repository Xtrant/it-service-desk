<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        {{ $ticket->ticket_number }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div class="ticket-show-page">

        <div class="ticket-show-container">

            {{-- =====================================================
             FLASH MESSAGE
        ====================================================== --}}

            @if (session('success'))
            <div class="ticket-alert ticket-alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="ticket-alert ticket-alert-error">
                {{ session('error') }}
            </div>
            @endif


            {{-- =====================================================
             VALIDATION ERROR
        ====================================================== --}}

            @if ($errors->any())
            <div class="ticket-alert ticket-alert-error">

                <div class="ticket-alert-title">
                    Terdapat kesalahan
                </div>

                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
            @endif


            {{-- =====================================================
             BREADCRUMB
        ====================================================== --}}

            <div class="ticket-show-breadcrumb">

                @if (auth()->user()->hasRole('employee'))

                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <span>›</span>

                <a href="{{ route('tickets.index') }}">
                    Ticket Saya
                </a>

                @elseif (auth()->user()->hasRole(['it_staff', 'admin']))

                <a href="{{ route('it.dashboard') }}">
                    Dashboard IT
                </a>

                <span>›</span>

                <a href="{{ route('tickets.index') }}">
                    Ticket Queue
                </a>

                @elseif (auth()->user()->hasRole('mis_supervisor'))

                <a href="{{ route('tickets.index') }}">
                    Approval
                </a>

                @endif

                <span>›</span>

                <span>
                    {{ $ticket->ticket_number }}
                </span>

            </div>


            {{-- =====================================================
             HEADER
        ====================================================== --}}

            <header class="ticket-show-header">

                <div class="ticket-show-header-main">

                    <div class="ticket-show-eyebrow">
                        MIS SERVICE DESK
                    </div>

                    <div class="ticket-show-number">
                        {{ $ticket->ticket_number }}
                    </div>

                    <h1>
                        {{ $ticket->title }}
                    </h1>

                    <div class="ticket-show-header-meta">

                        <span class="ticket-status-badge">
                            {{ $ticket->status?->name ?? 'Unknown' }}
                        </span>

                        <span>
                            {{ $ticket->requestType?->name ?? 'Layanan' }}
                        </span>

                        <span>·</span>

                        <span>
                            {{ $ticket->submitted_at?->format('d M Y H:i') ?? '-' }}
                        </span>

                    </div>

                </div>

                <div class="ticket-show-header-back">

                    <a
                        href="{{ route('tickets.index') }}"
                        class="ticket-back-button">

                        ← Kembali

                    </a>

                </div>

            </header>


            {{-- =====================================================
             TICKET INFORMATION
        ====================================================== --}}

            <section class="ticket-show-card">

                <div class="ticket-show-card-header">

                    <div>
                        <div class="ticket-show-card-eyebrow">
                            DETAIL
                        </div>

                        <h2>
                            Informasi Ticket
                        </h2>
                    </div>

                </div>


                <div class="ticket-info-grid">

                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            Requester
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->requester?->name ?? '-' }}
                        </span>

                    </div>


                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            Department
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->department?->name ?? '-' }}
                        </span>

                    </div>


                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            PIC
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->assignee?->name ?? 'Belum ada PIC' }}
                        </span>

                    </div>


                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            Priority
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->priority ?? '-' }}
                        </span>

                    </div>


                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            Request Type
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->requestType?->name ?? '-' }}
                        </span>

                    </div>


                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            Dibutuhkan
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->needed_at?->format('d M Y H:i') ?? '-' }}
                        </span>

                    </div>


                    @if ($ticket->asset)

                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            Asset
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->asset->asset_code }}
                            -
                            {{ $ticket->asset->name }}
                        </span>

                    </div>

                    @endif


                    @if ($ticket->resolved_at)

                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            Resolved
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->resolved_at->format('d M Y H:i') }}
                        </span>

                    </div>

                    @endif


                    @if ($ticket->closed_at)

                    <div class="ticket-info-item">

                        <span class="ticket-info-label">
                            Closed
                        </span>

                        <span class="ticket-info-value">
                            {{ $ticket->closed_at->format('d M Y H:i') }}
                        </span>

                    </div>

                    @endif

                </div>

            </section>


            @php
            $isSupportRole = auth()->user()->hasRole([
            'it_staff',
            'admin',
            ]);

            $canTakeTicket =
            $isSupportRole &&
            $ticket->assigned_to === null &&
            $ticket->status?->code === 'OPEN';

            $canStartTicket =
            $ticket->assigned_to === auth()->id() &&
            $ticket->status?->code === 'OPEN';

            $canResolveTicket =
            $ticket->assigned_to === auth()->id() &&
            $ticket->status?->code === 'IN_PROGRESS';

            $canConfirmClose =
            $ticket->requester_id === auth()->id() &&
            $ticket->status?->code === 'RESOLVED';

            $canReopen =
            in_array(
            $ticket->status?->code,
            [
            'RESOLVED',
            'CLOSED',
            ],
            true
            ) &&
            (
            $ticket->requester_id === auth()->id() ||
            $isSupportRole
            );

            $hasPrimaryAction =
            $canTakeTicket ||
            $canStartTicket ||
            $canResolveTicket ||
            $canConfirmClose ||
            $canReopen;
            @endphp


            {{-- =========================================================
         PRIMARY ACTION
    ========================================================== --}}

            @if ($hasPrimaryAction)

            <div class="ticket-action-card">

                <div class="ticket-show-card-header">
                    <div>
                        <div class="ticket-show-card-eyebrow">
                            ACTION
                        </div>

                        <h2>
                            Tindakan Ticket
                        </h2>
                    </div>
                </div>

                <div class="ticket-action-list">

                    {{-- Ambil Ticket --}}
                    @if (
                    auth()->user()->hasRole([
                    'it_staff',
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

                    <div class="ticket-content-form">

                        <h3 class="ticket-content-form-title">
                            Konfirmasi Penyelesaian
                        </h3>

                        <p class="ticket-confirmation-description">
                            IT telah menyelesaikan ticket ini.
                            Silakan periksa hasilnya. Jika sudah sesuai,
                            lakukan konfirmasi untuk menutup ticket.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('tickets.close', $ticket) }}"
                            id="close-ticket-form">

                            @csrf

                            <div class="ticket-content-form-group">

                                <label>
                                    Tanda Tangan Konfirmasi
                                </label>

                                <canvas
                                    id="signature-pad"
                                    width="600"
                                    height="250"
                                    class="ticket-signature-canvas"></canvas>

                            </div>

                            <input
                                type="hidden"
                                name="signature"
                                id="signature">

                            <div class="ticket-form-actions">

                                <button
                                    type="button"
                                    id="clear-signature"
                                    class="ticket-secondary-button">
                                    Bersihkan
                                </button>

                                <button
                                    type="submit"
                                    id="submit-close-ticket">
                                    Konfirmasi Selesai
                                </button>

                            </div>

                        </form>

                    </div>

                    @endif

                </div>

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

                <div class="ticket-content-form">
                    <div class="ticket-content-form-title">
                        Belum sesuai?
                    </div>

                    <form method="POST" action="{{ route('tickets.reopen', $ticket) }}">
                        @csrf

                        <div class="ticket-form-actions">
                            <button type="submit" class="ticket-reopen-button">
                                Buka Kembali Ticket
                            </button>
                        </div>
                    </form>
                </div>

                @endif


            </div>

            @endif


            {{-- =========================================================
     REQUEST DETAILS
========================================================== --}}

            @php
            $details = $ticket->details ?? [];
            $requestTypeCode = $ticket->requestType?->code;

            $detailFields = match ($requestTypeCode) {

            'HARDWARE_REPAIR' => [
            'device_name' => 'Nama Perangkat',
            'device_user' => 'Pengguna Perangkat',
            'location' => 'Lokasi',
            'problem' => 'Masalah',
            'since' => 'Sejak Terjadi',
            'impact' => 'Dampak',
            ],

            'HARDWARE_REPLACEMENT' => [
            'replacement_item' => 'Item yang Diganti',
            'parent_device' => 'Perangkat yang Terkait',
            'device_user' => 'Pengguna / Pemilik',
            'location' => 'Lokasi',
            'condition' => 'Kondisi Item Saat Ini',
            'replacement_reason' => 'Alasan Penggantian',
            ],

            'SOFTWARE' => [
            'software_name' => 'Nama Software',
            'purpose' => 'Tujuan Penggunaan',
            'version' => 'Versi',
            ],

            'APPLICATION' => [
            'app_name' => 'Nama Aplikasi',
            'request_kind' => 'Jenis Permintaan',
            'requirement' => 'Kebutuhan / Requirement',
            ],

            'DESIGN' => [
            'design_request' => 'Permintaan Design',
            'purpose' => 'Tujuan',
            ],

            'EMAIL_REQUEST' => [
            'email_user' => 'Pengguna Email',
            'email_address' => 'Alamat Email',
            'purpose' => 'Keperluan',
            ],

            'EMAIL_RELEASE' => [
            'email_address' => 'Alamat Email',
            'release_reason' => 'Alasan Pengeluaran',
            ],

            'EMAIL_CLOSURE' => [
            'email_address' => 'Alamat Email',
            'closure_reason' => 'Alasan Penutupan',
            ],

            default => [],
            };

            $requestKindLabels = [
            'NEW' => 'Aplikasi Baru',
            'CHANGE' => 'Perubahan Aplikasi',
            'PROBLEM' => 'Aplikasi Bermasalah',
            ];
            @endphp


            <section class="ticket-show-card ticket-request-card">

                <div class="ticket-show-card-header">

                    <div>
                        <div class="ticket-show-card-eyebrow">
                            REQUEST
                        </div>

                        <h2>
                            Detail Permintaan
                        </h2>
                    </div>

                </div>


                @if (empty($detailFields))

                <div class="ticket-request-empty">
                    Tidak ada detail permintaan.
                </div>

                @else

                <div class="ticket-request-grid">

                    @foreach ($detailFields as $key => $label)

                    @php
                    $value = $details[$key] ?? null;

                    if ($key === 'request_kind' && $value) {
                    $value = $requestKindLabels[$value] ?? $value;
                    }
                    @endphp

                    @if ($value !== null && $value !== '')

                    <div class="ticket-request-item">

                        <span class="ticket-info-label">
                            {{ $label }}
                        </span>

                        <div class="ticket-request-value">{{ trim($value) }}</div>

                    </div>

                    @endif

                    @endforeach

                </div>

                @endif

            </section>


            {{-- =========================================================
 CONVERSATION
========================================================== --}}

            <section class="ticket-content-section">

                <div class="ticket-content-section-header">

                    <div class="ticket-content-section-eyebrow">
                        COMMUNICATION
                    </div>

                    <h2>
                        Conversation
                    </h2>

                </div>


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

                <div class="ticket-entry">

                    <div class="ticket-entry-header">

                        <div class="ticket-entry-author">

                            {{ $comment->user?->name ?? 'User' }}

                            @if ($comment->is_internal)

                            <span class="ticket-entry-label">
                                Internal Note
                            </span>

                            @endif

                        </div>

                        <div class="ticket-entry-date">
                            {{ $comment->created_at?->format('d M Y H:i') }}
                        </div>

                    </div>

                    <p class="ticket-entry-text">{{ trim($comment->comment) }}</p>

                </div>

                @endif

                @empty

                <div class="ticket-content-empty">
                    Belum ada conversation.
                </div>

                @endforelse


                {{-- Tambah Comment --}}

                <button
                    type="button"
                    class="ticket-add-toggle"
                    data-toggle-target="comment-form"
                    data-open-label="− Tutup"
                    data-closed-label="+ Tambah Comment">
                    <span>+ Tambah Comment</span>
                </button>

                <div
                    id="comment-form"
                    class="ticket-content-form ticket-add-form"
                    hidden>
                    <h3 class="ticket-content-form-title">
                        Tambah Comment
                    </h3>

                    <form
                        method="POST"
                        action="{{ route('tickets.comments.store', $ticket) }}">

                        @csrf

                        <div class="ticket-content-form-group">

                            <label for="comment">
                                Comment
                            </label>

                            <textarea
                                id="comment"
                                name="comment"
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

                        <div class="ticket-content-form-group">

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

                </div>

            </section>

            {{-- =========================================================
     INSPECTION
========================================================== --}}

            <section class="ticket-content-section">

                <div class="ticket-content-section-header">

                    <div class="ticket-content-section-eyebrow">
                        VERIFICATION
                    </div>

                    <h2>
                        Inspection
                    </h2>

                </div>


                @forelse (
                $ticket->inspections->sortByDesc('inspected_at')
                as $inspection
                )

                <div class="ticket-entry">

                    <div class="ticket-entry-header">

                        <div class="ticket-entry-author">
                            {{ $inspection->inspector?->name ?? 'Inspector' }}
                        </div>

                        <div class="ticket-entry-date">
                            {{ $inspection->inspected_at?->format('d M Y H:i') }}
                        </div>

                    </div>


                    <div class="ticket-detail-row">
                        <div class="ticket-detail-label">
                            Result
                        </div>

                        <div class="ticket-detail-value">
                            {{ $inspection->result }}
                        </div>
                    </div>


                    @if ($inspection->notes)

                    <div class="ticket-detail-row">

                        <div class="ticket-detail-label">
                            Notes
                        </div>

                        <div class="ticket-detail-value ticket-detail-value-multiline">{{ trim($inspection->notes) }}</div>

                    </div>

                    @endif


                    @if ($inspection->signature_path)

                    <div class="ticket-signature-block">

                        <div class="ticket-signature-label">
                            Inspector Signature
                        </div>

                        <img
                            src="{{ route(
                            'tickets.signatures.inspection',
                            [
                                $ticket,
                                $inspection
                            ]
                        ) }}"
                            alt="Inspector Signature"
                            class="ticket-signature-image">

                    </div>

                    @endif

                </div>

                @empty

                <div class="ticket-content-empty">
                    Belum ada inspection.
                </div>

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

                <button
                    type="button"
                    class="ticket-add-toggle"
                    data-toggle-target="inspection-form"
                    data-open-label="− Tutup"
                    data-closed-label="+ Tambah Inspection">
                    <span>+ Tambah Inspection</span>
                </button>

                <div
                    id="inspection-form"
                    class="ticket-add-form"
                    hidden>

                    <form
                        method="POST"
                        action="{{ route(
            'tickets.inspections.store',
            $ticket
        ) }}"
                        id="inspection-form-submit">

                        @csrf


                        <div class="ticket-content-form-group">

                            <label for="result">
                                Hasil Pemeriksaan
                            </label>

                            <input
                                type="text"
                                id="result"
                                name="result"
                                maxlength="100"
                                required>

                        </div>


                        <div class="ticket-content-form-group">

                            <label for="inspection_notes">
                                Catatan Pemeriksaan
                            </label>

                            <textarea
                                id="inspection_notes"
                                name="notes"
                                maxlength="5000"
                                placeholder="Catatan hasil pemeriksaan..."></textarea>

                        </div>


                        <div class="ticket-content-form-group">

                            <label>
                                Tanda Tangan Inspector
                            </label>

                            <canvas
                                id="inspection-signature-pad"
                                width="600"
                                height="250"
                                class="ticket-signature-canvas"></canvas>

                        </div>


                        <input
                            type="hidden"
                            name="signature"
                            id="inspection-signature">


                        <div class="ticket-form-actions">

                            <button
                                type="button"
                                id="clear-inspection-signature"
                                class="ticket-secondary-button">
                                Bersihkan
                            </button>

                            <button
                                type="submit"
                                id="submit-inspection">
                                Simpan Inspection
                            </button>

                        </div>

                    </form>

                </div>

                @endif

            </section>

            {{-- =========================================================
     ACTION HISTORY
========================================================== --}}

            <section class="ticket-content-section">

                <div class="ticket-content-section-header">

                    <div class="ticket-content-section-eyebrow">
                        WORK LOG
                    </div>

                    <h2>
                        Action History
                    </h2>

                </div>


                @forelse (
                $ticket->actions->sortByDesc('performed_at')
                as $action
                )

                <div class="ticket-entry">

                    <div class="ticket-entry-header">

                        <div class="ticket-entry-author">
                            {{ $action->action_type }}
                        </div>

                        <div class="ticket-entry-date">
                            {{ $action->performed_at?->format('d M Y H:i') }}
                        </div>

                    </div>


                    @if ($action->description)

                    <div class="ticket-action-description">{{ trim($action->description) }}</div>

                    @endif

                    <div class="ticket-action-performer">
                        Dilakukan oleh
                        <strong>
                            {{ $action->performer?->name ?? 'User' }}
                        </strong>
                    </div>

                </div>

                @empty

                <div class="ticket-content-empty">
                    Belum ada action.
                </div>

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

                <button
                    type="button"
                    class="ticket-add-toggle"
                    data-toggle-target="action-form"
                    data-open-label="− Tutup"
                    data-closed-label="+ Tambah Action">
                    <span>+ Tambah Action</span>
                </button>

                <div
                    id="action-form"
                    class="ticket-content-form ticket-add-form"
                    hidden>
                    <h3 class="ticket-content-form-title">
                        Tambah Action
                    </h3>

                    <form
                        method="POST"
                        action="{{ route(
                    'tickets.actions.store',
                    $ticket
                ) }}">

                        @csrf


                        <div class="ticket-content-form-group">

                            <label for="action_type">
                                Jenis Tindakan
                            </label>

                            <input
                                type="text"
                                id="action_type"
                                name="action_type"
                                maxlength="50"
                                required
                                placeholder="Contoh: Troubleshooting">

                        </div>


                        <div class="ticket-content-form-group">

                            <label for="action_description">
                                Deskripsi Tindakan
                            </label>

                            <textarea
                                id="action_description"
                                name="description"
                                maxlength="5000"
                                placeholder="Jelaskan tindakan yang dilakukan..."></textarea>

                        </div>


                        <button type="submit">
                            Simpan Action
                        </button>

                    </form>

                </div>

                @endif

            </section>

            {{-- =========================================================
     ATTACHMENTS
========================================================== --}}

            <section class="ticket-content-section">

                <div class="ticket-content-section-header">

                    <div class="ticket-content-section-eyebrow">
                        FILES
                    </div>

                    <h2>
                        Attachments
                    </h2>

                </div>


                @forelse (
                $ticket->attachments->sortByDesc('created_at')
                as $attachment
                )

                <div class="ticket-file-item">

                    <div class="ticket-file-name">
                        {{ $attachment->file_name }}
                    </div>


                    <div class="ticket-file-meta">

                        {{ $attachment->file_size
                    ? number_format(
                        $attachment->file_size / 1024,
                        1
                    ) . ' KB'
                    : '-'
                }}

                        <span>·</span>

                        Upload oleh
                        {{ $attachment->user?->name ?? 'User' }}

                        <span>·</span>

                        {{ $attachment->created_at?->format('d M Y H:i') }}

                    </div>


                    <div class="ticket-file-actions">

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

                </div>

                @empty

                <div class="ticket-content-empty">
                    Belum ada attachment.
                </div>

                @endforelse


                {{-- Tambah Attachment --}}

                @if (
                auth()->user()->hasRole([
                'employee',
                'it_staff',
                'mis_supervisor',
                'admin',
                ])
                )

                <button
                    type="button"
                    class="ticket-add-toggle"
                    data-toggle-target="attachment-form"
                    data-open-label="− Tutup"
                    data-closed-label="+ Tambah Attachment">
                    <span>+ Tambah Attachment</span>
                </button>

                <div
                    id="attachment-form"
                    class="ticket-add-form"
                    hidden>

                    <form
                        method="POST"
                        action="{{ route(
                    'tickets.attachments.store',
                    $ticket
                ) }}"
                        enctype="multipart/form-data">

                        @csrf


                        <div class="ticket-content-form-group">

                            <label for="file">
                                File
                            </label>

                            <input
                                type="file"
                                id="file"
                                name="file"
                                required>

                        </div>


                        <div class="ticket-file-meta">
                            Maksimal 10 MB · JPG, PNG, PDF, DOC, DOCX, XLS, XLSX
                        </div>


                        <button type="submit">
                            Upload Attachment
                        </button>

                    </form>

                </div>

                @endif

            </section>


            {{-- =========================================================
     APPROVAL
========================================================== --}}

            <section class="ticket-content-section">

                <div class="ticket-content-section-header">

                    <div class="ticket-content-section-eyebrow">
                        AUTHORIZATION
                    </div>

                    <h2>
                        Approval
                    </h2>

                </div>


                @forelse (
                $ticket->approvals->sortByDesc('created_at')
                as $approval
                )

                <div class="ticket-approval-item">

                    <div class="ticket-entry-header">

                        <div class="ticket-entry-author">
                            {{ $approval->approval_type }}
                        </div>

                        <div class="ticket-entry-date">
                            {{ $approval->created_at?->format('d M Y H:i') }}
                        </div>

                    </div>


                    <div class="ticket-approval-meta">

                        Approver:
                        {{ $approval->approver?->name ?? '-' }}

                    </div>


                    <div class="ticket-approval-status">

                        Status:
                        <strong>
                            {{ $approval->status }}
                        </strong>

                    </div>


                    @if ($approval->notes)

                    <div class="ticket-approval-notes">

                        <div class="ticket-approval-notes-label">
                            Notes
                        </div>

                        <div class="ticket-approval-notes-value">{{ trim($approval->notes) }}</div>

                    </div>

                    @endif


                    @if ($approval->approved_at)

                    <div class="ticket-approval-meta">

                        Diproses:
                        {{ $approval->approved_at->format('d M Y H:i') }}

                    </div>

                    @endif


                    @if ($approval->signature_path)

                    <div class="ticket-signature-block">

                        <div class="ticket-info-label">
                            Approval Signature
                        </div>

                        <img
                            src="{{ route(
                            'tickets.signatures.approval',
                            [
                                $ticket,
                                $approval
                            ]
                        ) }}"
                            alt="Approval Signature"
                            class="ticket-signature-image">

                    </div>

                    @endif


                    {{-- APPROVE / REJECT --}}

                    @if (
                    $approval->status === 'PENDING'
                    &&
                    $approval->approver_id === auth()->id()
                    )

                    <button
                        type="button"
                        class="ticket-add-toggle"
                        data-toggle-target="approval-action-form-{{ $approval->id }}"
                        data-open-label="− Tutup"
                        data-closed-label="+ Approval Action">
                        <span>+ Approval Action</span>
                    </button>

                    <div
                        id="approval-action-form-{{ $approval->id }}"
                        class="ticket-content-form ticket-add-form"
                        hidden>

                        <h3 class="ticket-content-form-title">
                            Approval Action
                        </h3>


                        {{-- APPROVE --}}

                        <form
                            method="POST"
                            action="{{ route(
                            'tickets.approvals.approve',
                            [
                                $ticket,
                                $approval
                            ]
                        ) }}"
                            id="approval-form-{{ $approval->id }}">

                            @csrf


                            <div class="ticket-content-form-group">

                                <label
                                    for="approval-notes-{{ $approval->id }}">

                                    Catatan Approval

                                </label>

                                <textarea
                                    id="approval-notes-{{ $approval->id }}"
                                    name="notes"
                                    maxlength="5000"
                                    placeholder="Catatan approval...">{{ old('notes') }}</textarea>

                            </div>


                            <div class="ticket-content-form-group">

                                <label>
                                    Tanda Tangan Approval
                                </label>

                                <canvas
                                    id="approval-signature-pad-{{ $approval->id }}"
                                    width="600"
                                    height="250"
                                    class="ticket-signature-canvas"></canvas>

                            </div>


                            <input
                                type="hidden"
                                name="signature"
                                id="approval-signature-{{ $approval->id }}">


                            <div class="ticket-form-actions">

                                <button
                                    type="button"
                                    class="ticket-secondary-button"
                                    onclick="clearApprovalSignature({{ $approval->id }})">

                                    Bersihkan

                                </button>

                                <button
                                    type="submit"
                                    onclick="return submitApprovalSignature({{ $approval->id }})">

                                    Approve

                                </button>

                            </div>

                        </form>


                        {{-- REJECT --}}

                        <form
                            method="POST"
                            action="{{ route(
                            'tickets.approvals.reject',
                            [
                                $ticket,
                                $approval
                            ]
                        ) }}"
                            class="ticket-reject-form">

                            @csrf

                            <div class="ticket-content-form-group">

                                <label>
                                    Alasan Penolakan
                                </label>

                                <textarea
                                    name="notes"
                                    maxlength="5000"
                                    required
                                    placeholder="Jelaskan alasan penolakan..."></textarea>

                            </div>


                            <button type="submit">
                                Reject
                            </button>

                        </form>

                    </div>

                    @endif

                </div>

                @empty

                <div class="ticket-content-empty">
                    Belum ada approval.
                </div>

                @endforelse


                {{-- =====================================================
         REQUEST APPROVAL
    ====================================================== --}}

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

                <button
                    type="button"
                    class="ticket-add-toggle"
                    data-toggle-target="request-approval-form"
                    data-open-label="− Tutup"
                    data-closed-label="+ Request Approval">
                    <span>+ Request Approval</span>
                </button>

                <div
                    id="request-approval-form"
                    class="ticket-content-form ticket-add-form"
                    hidden>

                    <h3 class="ticket-content-form-title">
                        Request Approval
                    </h3>


                    @if ($approver)

                    <div class="ticket-approval-request-info">

                        Approval akan dikirim kepada:

                        <strong>
                            {{ $approver->name }}
                        </strong>

                        <span>
                            {{ $approver->position ?? '-' }}
                        </span>

                    </div>


                    <form
                        method="POST"
                        action="{{ route(
                            'tickets.approvals.store',
                            $ticket
                        ) }}">

                        @csrf


                        <div class="ticket-content-form-group">

                            <label for="approval_type">
                                Approval Type
                            </label>

                            <input
                                type="text"
                                id="approval_type"
                                name="approval_type"
                                value="{{ $ticket->requestType?->name }}"
                                maxlength="50"
                                required>

                        </div>


                        <div class="ticket-content-form-group">

                            <label for="approval_notes">
                                Notes
                            </label>

                            <textarea
                                id="approval_notes"
                                name="notes"
                                maxlength="5000"
                                placeholder="Catatan untuk approver...">{{ old('notes') }}</textarea>

                        </div>


                        <button type="submit">
                            Request Approval
                        </button>

                    </form>

                    @else

                    <div class="ticket-content-empty">

                        Approval belum dapat diajukan.
                        Requester belum memiliki atasan
                        yang terdaftar di sistem.

                    </div>

                    @endif

                </div>

                @endif

                @endif

            </section>

            {{-- =========================================================
 ACTIVITY TIMELINE
========================================================== --}}

            <section class="ticket-content-section">

                <div class="ticket-content-section-header">

                    <div class="ticket-content-section-eyebrow">
                        AUDIT
                    </div>

                    <h2>
                        Activity Timeline
                    </h2>

                </div>


                @forelse (
                $ticket->activities->sortByDesc('created_at')
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

                'attachment_added'
                => 'Attachment ditambahkan',

                'attachment_deleted'
                => 'Attachment dihapus',

                default
                => ucfirst(
                str_replace(
                '_',
                ' ',
                $activity->activity_type
                )
                ),
                };

                @endphp


                <div class="ticket-activity-item">

                    <div class="ticket-activity-header">

                        <div class="ticket-activity-title">
                            {{ $activityLabel }}
                        </div>

                        <div class="ticket-activity-date">
                            {{ $activity->created_at?->format('d M Y H:i') }}
                        </div>

                    </div>


                    @if ($activity->description)

                    <div class="ticket-activity-description">{{ trim($activity->description) }}</div>

                    @endif


                    {{-- Assigned --}}

                    @if ($activity->activity_type === 'assigned')

                    <div class="ticket-activity-meta-row">

                        <span>PIC</span>

                        <strong>
                            {{ $activity->user?->name ?? '-' }}
                        </strong>

                    </div>


                    {{-- Status Change --}}

                    @elseif ($activity->activity_type === 'status_changed')

                    @if (
                    $activity->old_value !== null ||
                    $activity->new_value !== null
                    )

                    <div class="ticket-status-change">

                        @if ($activity->old_value !== null)

                        <div class="ticket-status-change-row">
                            <span>Dari</span>
                            <strong>
                                {{ str_replace('_', ' ', $activity->old_value) }}
                            </strong>
                        </div>

                        @endif

                        @if ($activity->new_value !== null)

                        <div class="ticket-status-change-row">
                            <span>Menjadi</span>
                            <strong>
                                {{ str_replace('_', ' ', $activity->new_value) }}
                            </strong>
                        </div>

                        @endif

                    </div>

                    @endif

                    @endif


                    <div class="ticket-activity-by">
                        Oleh
                        <strong>{{ $activity->user?->name ?? 'System' }}</strong>
                    </div>

                </div>

                @empty

                <div class="ticket-content-empty">
                    Belum ada activity.
                </div>

                @endforelse

            </section>

        </div>

    </div>

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

            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;

            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        function setupSignatureCanvas(canvas, onDraw) {
            if (!canvas) {
                return null;
            }

            const ctx = canvas.getContext('2d');

            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#111827';

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

                    requesterPad.ctx.lineWidth = 2.5;
                    requesterPad.ctx.lineCap = 'round';
                    requesterPad.ctx.lineJoin = 'round';
                    requesterPad.ctx.strokeStyle = '#111827';

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

                    inspectionPad.ctx.lineWidth = 2.5;
                    inspectionPad.ctx.lineCap = 'round';
                    inspectionPad.ctx.lineJoin = 'round';
                    inspectionPad.ctx.strokeStyle = '#111827';

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
                            'inspection-form-submit'
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

        document.querySelectorAll('.ticket-add-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.dataset.toggleTarget;
                const target = document.getElementById(targetId);

                if (!target) {
                    return;
                }

                target.hidden = !target.hidden;

                const label = target.hidden ?
                    button.dataset.closedLabel :
                    button.dataset.openLabel;

                const text = button.querySelector('span');

                if (text) {
                    text.textContent = label;
                }
            });
        });
    </script>

</body>

</html>