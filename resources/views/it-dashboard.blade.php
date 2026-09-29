@extends('layouts.app')

@section('title', 'Dashboard IT - IT Service Desk')

@section('content')

{{-- HERO --}}
<section class="dashboard-hero">

    <div class="dashboard-eyebrow">
        IT SERVICE DESK
    </div>

    <h1>
        Dashboard IT
    </h1>

    <p>
        Monitor dan tangani ticket yang masuk
        ke IT Service Desk.
    </p>

</section>


{{-- SUMMARY --}}
<section class="dashboard-summary">

    <div class="dashboard-summary-card">

        <div class="dashboard-summary-label">
            Total Ticket
        </div>

        <div class="dashboard-summary-value">
            {{ $totalTickets }}
        </div>

    </div>


    <div class="dashboard-summary-card">

        <div class="dashboard-summary-label">
            Open
        </div>

        <div class="dashboard-summary-value">
            {{ $openTickets }}
        </div>

    </div>


    <div class="dashboard-summary-card">

        <div class="dashboard-summary-label">
            In Progress
        </div>

        <div class="dashboard-summary-value">
            {{ $inProgressTickets }}
        </div>

    </div>


    <div class="dashboard-summary-card">

        <div class="dashboard-summary-label">
            Resolved
        </div>

        <div class="dashboard-summary-value">
            {{ $resolvedTickets }}
        </div>

    </div>


    <div class="dashboard-summary-card">

        <div class="dashboard-summary-label">
            Belum Diambil
        </div>

        <div class="dashboard-summary-value">
            {{ $unassignedTickets }}
        </div>

    </div>


    <div class="dashboard-summary-card">

        <div class="dashboard-summary-label">
            Ticket Saya
        </div>

        <div class="dashboard-summary-value">
            {{ $myTickets }}
        </div>

    </div>

</section>


{{-- QUICK ACTION --}}
<section>

    <div class="dashboard-section-label">
        Quick Action
    </div>

    <div class="dashboard-service-grid">

        <a
            href="{{ route('tickets.index', ['status' => 'OPEN', 'assignment' => 'UNASSIGNED']) }}"
            class="dashboard-service-card">

            <div class="dashboard-service-icon">
                📥
            </div>

            <h2>
                Ticket Open
            </h2>

            <p>
                Lihat ticket yang belum selesai
                dan siap ditangani.
            </p>

            <div class="dashboard-service-arrow">
                →
            </div>

        </a>


        <a
            href="{{ route('tickets.index', ['status' => 'IN_PROGRESS']) }}"
            class="dashboard-service-card">

            <div class="dashboard-service-icon">
                🔧
            </div>

            <h2>
                Sedang Ditangani
            </h2>

            <p>
                Lihat ticket yang sedang
                dalam proses penanganan.
            </p>

            <div class="dashboard-service-arrow">
                →
            </div>

        </a>


        <a
            href="{{ route('tickets.index') }}"
            class="dashboard-service-card">

            <div class="dashboard-service-icon">
                📋
            </div>

            <h2>
                Semua Ticket
            </h2>

            <p>
                Buka seluruh daftar ticket
                IT Service Desk.
            </p>

            <div class="dashboard-service-arrow">
                →
            </div>

        </a>

    </div>

</section>


{{-- TICKET TERBARU --}}
<section class="dashboard-tickets">

    <div class="dashboard-tickets-header">

        <h2 class="dashboard-tickets-title">
            Ticket Terbaru
        </h2>

        <a
            href="{{ route('tickets.index') }}"
            class="dashboard-tickets-link">
            Lihat semua →
        </a>

    </div>


    @if ($recentTickets->isEmpty())

    <div class="ticket-empty">

        <div class="ticket-empty-title">
            Belum ada ticket
        </div>

        <div class="ticket-empty-text">
            Ticket yang masuk ke IT Service Desk
            akan muncul di sini.
        </div>

    </div>

    @else

    <div class="dashboard-ticket-list">

        @foreach ($recentTickets as $ticket)

        <a
            href="{{ route('tickets.show', $ticket) }}"
            class="dashboard-ticket-item">

            <div class="dashboard-ticket-main">

                <div class="dashboard-ticket-number">
                    {{ $ticket->ticket_number }}
                </div>

                <div class="dashboard-ticket-title">
                    {{ $ticket->title }}
                </div>

                <div class="dashboard-ticket-meta">

                    {{ $ticket->requestType?->name ?? 'Layanan' }}

                    <span>·</span>

                    {{ $ticket->requester?->name ?? '-' }}

                    <span>·</span>

                    {{ $ticket->department?->name ?? '-' }}

                </div>

            </div>


            <div class="dashboard-ticket-side">

                <span class="dashboard-ticket-status">
                    {{ $ticket->status?->name ?? 'Unknown' }}
                </span>

                <span class="dashboard-ticket-priority">
                    {{ $ticket->priority }}
                </span>

                <span class="dashboard-ticket-pic">
                    @if ($ticket->assigned_to)
                    PIC: {{ $ticket->assignee?->name ?? '-' }}
                    @else
                    Belum Diambil
                    @endif
                </span>

                <span class="dashboard-ticket-arrow">
                    →
                </span>

            </div>

        </a>

        @endforeach

    </div>

    @endif

</section>

@endsection