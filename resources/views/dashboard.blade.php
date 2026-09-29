@extends('layouts.app')

@section('title', 'Dashboard - IT Service Desk')

@section('content')

    {{-- HERO --}}
    <section class="dashboard-hero">

        <div class="dashboard-eyebrow">
            MIS SERVICE DESK
        </div>

        <h1>
            Apa yang Anda butuhkan?
        </h1>

        <p>
            Pilih jenis bantuan yang Anda perlukan.
            Tim MIS akan membantu menangani permintaan Anda.
        </p>

    </section>


    {{-- CATEGORY --}}
    <section>

        <div class="dashboard-section-label">
            Pilih layanan
        </div>

        <div class="dashboard-service-grid">

            {{-- HARDWARE / SOFTWARE --}}
            <a
                href="{{ route('tickets.create', ['category' => 'hardware']) }}"
                class="dashboard-service-card">

                <div class="dashboard-service-icon">
                    🖥
                </div>

                <h2>
                    Hardware / Software
                </h2>

                <p>
                    Perbaikan atau penggantian perangkat
                    serta permintaan software.
                </p>

                <div class="dashboard-service-arrow">
                    →
                </div>

            </a>


            {{-- APPLICATION / DESIGN --}}
            <a
                href="{{ route('tickets.create', ['category' => 'application']) }}"
                class="dashboard-service-card">

                <div class="dashboard-service-icon">
                    ⚙
                </div>

                <h2>
                    Aplikasi / Design
                </h2>

                <p>
                    Pengembangan, perubahan, atau masalah
                    aplikasi serta kebutuhan graphic design.
                </p>

                <div class="dashboard-service-arrow">
                    →
                </div>

            </a>


            {{-- EMAIL --}}
            <a
                href="{{ route('tickets.create', ['category' => 'email']) }}"
                class="dashboard-service-card">

                <div class="dashboard-service-icon">
                    ✉
                </div>

                <h2>
                    Email
                </h2>

                <p>
                    Permintaan, pengeluaran, atau
                    penutupan email perusahaan.
                </p>

                <div class="dashboard-service-arrow">
                    →
                </div>

            </a>

        </div>

    </section>


    {{-- TICKET SUMMARY --}}
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
                Ticket Aktif
            </div>

            <div class="dashboard-summary-value">
                {{ $activeTickets }}
            </div>

        </div>


        <div class="dashboard-summary-card">

            <div class="dashboard-summary-label">
                Ticket Selesai
            </div>

            <div class="dashboard-summary-value">
                {{ $closedTickets }}
            </div>

        </div>

    </section>


    {{-- MY TICKETS --}}
    <section class="dashboard-tickets">

        <div class="dashboard-tickets-header">

            <h2 class="dashboard-tickets-title">
                Ticket Saya
            </h2>

            <a
                href="{{ route('tickets.index') }}"
                class="dashboard-tickets-link">
                Lihat semua →
            </a>

        </div>


        @if ($tickets->isEmpty())

            <div class="ticket-empty">

                <div class="ticket-empty-title">
                    Belum ada ticket
                </div>

                <div class="ticket-empty-text">
                    Ticket yang Anda ajukan akan muncul di sini.
                </div>

            </div>

        @else

            <div class="dashboard-ticket-list dashboard-ticket-list-employee">

                @foreach ($tickets as $ticket)

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

                                {{ $ticket->submitted_at?->format('d M Y H:i') ?? '-' }}
                            </div>

                        </div>


                        <div class="dashboard-ticket-side">

                            <span class="dashboard-ticket-status">
                                {{ $ticket->status?->name ?? 'Unknown' }}
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