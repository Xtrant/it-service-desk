@extends('layouts.app')

@section('title', 'Tickets - IT Service Desk')

@section('content')

<section class="dashboard-hero">

    <div class="dashboard-eyebrow">
        IT SERVICE DESK
    </div>

    <h1>
        Ticket Queue
    </h1>

    <p>
        Kelola dan pantau seluruh ticket
        yang masuk ke IT Service Desk.
    </p>

</section>


{{-- FILTER --}}
<section class="dashboard-tickets">

    <div class="dashboard-tickets-header">

        <h2 class="dashboard-tickets-title">
            Daftar Ticket
        </h2>

    </div>

    <form
        method="GET"
        action="{{ route('tickets.index') }}"
        class="ticket-filter-form">

        <input
            type="text"
            name="search"
            placeholder="Cari nomor, judul, requester, NIP, atau department..."
            value="{{ request('search') }}">

        <select name="status">
            <option value="">
                Semua Status
            </option>

            @foreach (\App\Models\TicketStatus::orderBy('id')->get() as $status)
            <option
                value="{{ $status->code }}"
                @selected(request('status')===$status->code)>
                {{ $status->name }}
            </option>
            @endforeach
        </select>


        <select name="priority">

            <option value="">
                Semua Priority
            </option>

            <option
                value="LOW"
                @selected(request('priority')==='LOW' )>
                Low
            </option>

            <option
                value="NORMAL"
                @selected(request('priority')==='NORMAL' )>
                Normal
            </option>

            <option
                value="HIGH"
                @selected(request('priority')==='HIGH' )>
                High
            </option>

            <option
                value="URGENT"
                @selected(request('priority')==='URGENT' )>
                Urgent
            </option>

        </select>

        <select name="assignee">
            <option value="">
                Semua PIC
            </option>

            <option
                value="UNASSIGNED"
                @selected(request('assignee')==='UNASSIGNED' )>
                Belum Diambil
            </option>

            <option
                value="ASSIGNED"
                @selected(request('assignee')==='ASSIGNED' )>
                Sudah Diambil
            </option>

            @foreach ($itStaff as $staff)
            <option
                value="{{ $staff->id }}"
                @selected((string) request('assignee')===(string) $staff->id)>
                {{ $staff->name }}
            </option>
            @endforeach
        </select>


        <button type="submit">
            Filter
        </button>

        <a href="{{ route('tickets.index') }}">
            Reset
        </a>

    </form>


    {{-- TICKET LIST --}}
    @if ($tickets->isEmpty())

    <div class="ticket-empty">

        <div class="ticket-empty-title">
            Tidak ada ticket
        </div>

        <div class="ticket-empty-text">
            Tidak ditemukan ticket berdasarkan filter yang dipilih.
        </div>

    </div>

    @else

    <div class="dashboard-ticket-list">

        @foreach ($tickets as $ticket)

        <a
            href="{{ route('tickets.show', $ticket) }}"
            class="dashboard-ticket-item {{ $ticket->assigned_to === null && $ticket->status?->code === 'OPEN' ? 'dashboard-ticket-unassigned' : '' }}">

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

                @if (
                auth()->user()->hasRole(['it_staff', 'admin']) &&
                $ticket->assigned_to === null &&
                $ticket->status?->code === 'OPEN'
                )
                <div
                    class="dashboard-ticket-take-form"
                    onclick="event.preventDefault(); event.stopPropagation();">

                    <form
                        method="POST"
                        action="{{ route('tickets.take', $ticket) }}">
                        @csrf

                        <button
                            type="submit"
                            class="dashboard-ticket-take">
                            Ambil
                        </button>
                    </form>

                </div>
                @else
                <span class="dashboard-ticket-arrow">
                    →
                </span>
                @endif

            </div>
        </a>

        @endforeach

    </div>


    <div class="mt-6">
        {{ $tickets->links() }}
    </div>

    @endif

</section>

@endsection