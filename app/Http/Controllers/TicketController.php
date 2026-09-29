<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\RequestType;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;


class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Ticket::query()
            ->with([
                'requester',
                'department',
                'requestType',
                'status',
                'assignee',
            ])
            ->latest('created_at');

        // Employee hanya melihat ticket miliknya
        if ($user->hasRole('employee')) {
            $query->where('requester_id', $user->id);
        }


        if ($user->hasRole('mis_supervisor')) {
            $query->whereHas('approvals', function ($q) use ($user) {
                $q->where('approver_id', $user->id)
                    ->where('status', 'PENDING');
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('code', $request->status);
            });
        }

        if ($request->filled('assignment')) {
            if ($request->assignment === 'UNASSIGNED') {
                $query->whereNull('assigned_to');
            } elseif ($request->assignment === 'ASSIGNED') {
                $query->whereNotNull('assigned_to');
            }
        }

        // Filter priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter PIC
        if ($request->filled('assignee')) {
            if ($request->assignee === 'UNASSIGNED') {
                $query->whereNull('assigned_to');
            } elseif ($request->assignee === 'ASSIGNED') {
                $query->whereNotNull('assigned_to');
            } elseif (is_numeric($request->assignee)) {
                $query->where('assigned_to', (int) $request->assignee);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->string('search')->trim();

            $query->where(function ($q) use ($search) {

                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")

                    ->orWhereHas('requester', function ($requesterQuery) use ($search) {
                        $requesterQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('employee_number', 'like', "%{$search}%");
                    })

                    ->orWhereHas('department', function ($departmentQuery) use ($search) {
                        $departmentQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $itStaff = User::query()
            ->where('is_active', true)
            ->whereHas('role', function ($query) {
                $query->whereIn('name', [
                    'it_staff',
                    'admin',
                ]);
            })
            ->orderBy('name')
            ->get();

        $tickets = $query
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', compact(
            'tickets',
            'itStaff'
        ));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Ticket::class);

        $category = $request->query('category');

        $allowedCategories = [
            'hardware',
            'application',
            'email',
        ];

        if (! in_array($category, $allowedCategories, true)) {
            $category = null;
        }

        $categoryCodes = match ($category) {
            'hardware' => [
                'HARDWARE_REPAIR',
                'HARDWARE_REPLACEMENT',
                'SOFTWARE',
            ],

            'application' => [
                'APPLICATION',
                'DESIGN',
            ],

            'email' => [
                'EMAIL_REQUEST',
                'EMAIL_RELEASE',
                'EMAIL_CLOSURE',
            ],

            default => null,
        };

        $requestTypes = RequestType::query()
            ->where('is_active', true)
            ->when(
                $categoryCodes,
                fn($query) => $query->whereIn('code', $categoryCodes)
            )
            ->orderBy('name')
            ->get();

        return view('tickets.create', compact(
            'requestTypes',
            'category'
        ));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        Gate::authorize('create', Ticket::class);

        $requestType = RequestType::query()
            ->where('id', $request->input('request_type_id'))
            ->where('is_active', true)
            ->firstOrFail();

        $details = $request->input('details', []);

        $validated = $request->validate([
            'request_type_id' => [
                'required',
                'integer',
                'exists:request_types,id',
            ],

            'asset_id' => [
                'nullable',
                'integer',
                'exists:assets,id',
            ],

            'priority' => [
                'required',
                'in:LOW,NORMAL,HIGH,URGENT',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'needed_at' => [
                'nullable',
                'date',
            ],

            'details' => [
                'nullable',
                'array',
            ],
        ]);

        switch ($requestType->code) {
            case 'HARDWARE_REPAIR':
                validator($details, [
                    'device_name' => ['required', 'string', 'max:200'],
                    'device_user' => ['nullable', 'string', 'max:200'],
                    'location' => ['nullable', 'string', 'max:200'],
                    'problem' => ['required', 'string', 'max:2000'],
                    'since' => ['nullable', 'date'],
                    'impact' => ['nullable', 'string', 'max:2000'],
                ])->validate();

                break;

            case 'HARDWARE_REPLACEMENT':
                validator($details, [
                    'replacement_item' => ['required', 'string', 'max:200'],
                    'parent_device' => ['nullable', 'string', 'max:200'],
                    'device_user' => ['nullable', 'string', 'max:200'],
                    'location' => ['nullable', 'string', 'max:200'],
                    'condition' => ['required', 'string', 'max:2000'],
                    'replacement_reason' => ['required', 'string', 'max:2000'],
                ])->validate();

                break;

            case 'SOFTWARE':
                validator($details, [
                    'software_name' => ['required', 'string', 'max:200'],
                    'purpose' => ['required', 'string', 'max:2000'],
                    'version' => ['nullable', 'string', 'max:100'],
                ])->validate();

                break;

            case 'APPLICATION':
                validator($details, [
                    'app_name' => ['required', 'string', 'max:200'],
                    'request_kind' => ['required', 'in:NEW,CHANGE,PROBLEM'],
                    'requirement' => ['required', 'string', 'max:5000'],
                ])->validate();

                break;

            case 'DESIGN':
                validator($details, [
                    'design_request' => ['required', 'string', 'max:2000'],
                    'purpose' => ['nullable', 'string', 'max:500'],
                ])->validate();

                break;

            case 'EMAIL_REQUEST':
                validator($details, [
                    'email_user' => ['required', 'string', 'max:200'],
                    'purpose' => ['required', 'string', 'max:2000'],
                    'email_address' => ['nullable', 'email', 'max:200'],
                ])->validate();
                break;

            case 'EMAIL_RELEASE':
                validator($details, [
                    'email_address' => ['required', 'email', 'max:200'],
                    'release_reason' => ['required', 'string', 'max:2000'],
                ])->validate();
                break;

            case 'EMAIL_CLOSURE':
                validator($details, [
                    'email_address' => ['required', 'email', 'max:200'],
                    'closure_reason' => ['required', 'string', 'max:2000'],
                ])->validate();
                break;
        }

        switch ($requestType->code) {
            case 'HARDWARE_REPAIR':
                $details = [
                    'device_name' => $details['device_name'] ?? null,
                    'device_user' => $details['device_user'] ?? null,
                    'location' => $details['location'] ?? null,
                    'problem' => $details['problem'] ?? null,
                    'since' => $details['since'] ?? null,
                    'impact' => $details['impact'] ?? null,
                ];
                break;

            case 'HARDWARE_REPLACEMENT':
                $details = [
                    'replacement_item' => $details['replacement_item'] ?? null,
                    'parent_device' => $details['parent_device'] ?? null,
                    'device_user' => $details['device_user'] ?? null,
                    'location' => $details['location'] ?? null,
                    'condition' => $details['condition'] ?? null,
                    'replacement_reason' => $details['replacement_reason'] ?? null,
                ];

                break;

            case 'SOFTWARE':
                $details = [
                    'software_name' => $details['software_name'] ?? null,
                    'purpose' => $details['purpose'] ?? null,
                    'version' => $details['version'] ?? null,
                ];
                break;

            case 'APPLICATION':
                $details = [
                    'app_name' => $details['app_name'] ?? null,
                    'request_kind' => $details['request_kind'] ?? null,
                    'requirement' => $details['requirement'] ?? null,
                ];

                break;

            case 'DESIGN':
                $details = [
                    'design_request' => $details['design_request'] ?? null,
                    'purpose' => $details['purpose'] ?? null,
                ];

                break;

            case 'EMAIL_REQUEST':
                $details = [
                    'email_user' => $details['email_user'] ?? null,
                    'purpose' => $details['purpose'] ?? null,
                    'email_address' => $details['email_address'] ?? null,
                ];
                break;

            case 'EMAIL_RELEASE':
                $details = [
                    'email_address' => $details['email_address'] ?? null,
                    'release_reason' => $details['release_reason'] ?? null,
                ];
                break;

            case 'EMAIL_CLOSURE':
                $details = [
                    'email_address' => $details['email_address'] ?? null,
                    'closure_reason' => $details['closure_reason'] ?? null,
                ];
                break;

            default:
                $details = [];
                break;
        }

        $title = match ($requestType->code) {
            'HARDWARE_REPAIR' =>
            'Perbaikan Hardware - ' . ($details['replacement_item'] ?? 'Perangkat'),

            'HARDWARE_REPLACEMENT' =>
            'Penggantian Hardware - ' . ($details['replacement_item'] ?? 'Perangkat'),

            'SOFTWARE' =>
            'Permintaan Software - ' . ($details['software_name'] ?? 'Software'),

            'APPLICATION' =>
            match ($details['request_kind'] ?? null) {
                'NEW' =>
                'Aplikasi Baru - ' . ($details['app_name'] ?? 'Aplikasi'),

                'CHANGE' =>
                'Perubahan Aplikasi - ' . ($details['app_name'] ?? 'Aplikasi'),

                'PROBLEM' =>
                'Aplikasi Bermasalah - ' . ($details['app_name'] ?? 'Aplikasi'),

                default =>
                'Pembuatan / Pengembangan Aplikasi - ' .
                    ($details['app_name'] ?? 'Aplikasi'),
            },

            'DESIGN' =>
            'Graphic Design - ' .
                ($details['design_request'] ?? 'Kebutuhan Desain'),

            'EMAIL_REQUEST' =>
            'Permintaan Email - ' . (
                $details['email_address']
                ?? $details['email_user']
                ?? 'Pengguna'
            ),

            'EMAIL_RELEASE' =>
            'Pengeluaran Email - ' . (
                $details['email_address']
                ?? 'Email'
            ),

            'EMAIL_CLOSURE' =>
            'Penutupan Email - ' . (
                $details['email_address']
                ?? 'Email'
            ),

            default =>
            $requestType->name,
        };

        $status = TicketStatus::where('code', 'OPEN')->firstOrFail();

        $ticket = DB::transaction(function () use (
            $validated,
            $details,
            $title,
            $user,
            $status
        ) {
            $ticket = Ticket::create([
                'ticket_number' => $this->generateTicketNumber(),

                'requester_id' => $user->id,

                'department_id' => $user->department_id,

                'request_type_id' => $validated['request_type_id'],

                'asset_id' => $validated['asset_id'] ?? null,

                'status_id' => $status->id,

                'assigned_to' => null,

                'priority' => $validated['priority'],

                'title' => $title,

                'description' => $validated['description'] ?? null,

                'details' => $details ?: null,

                'needed_at' => $validated['needed_at'] ?? null,

                'submitted_at' => now(),
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'created',
                'description' => 'Ticket dibuat.',
            ]);

            return $ticket;
        });

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket berhasil dibuat.');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $ticket->load([
            'requester',
            'department',
            'requestType',
            'asset',
            'status',
            'assignee',
            'comments.user',
            'attachments',
            'activities.user',
            'inspections',
            'actions',
            'approvals',
        ]);

        $approvers = User::query()
            ->where('is_active', true)
            ->whereHas('role', function ($query) {
                $query->whereIn('name', [
                    'mis_supervisor',
                    'admin',
                ]);
            })
            ->orderBy('name')
            ->get();

        return view('tickets.show', compact('ticket'));
    }

    public function take(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('handle', $ticket);

        if ($ticket->assigned_to !== null) {
            return back()->with('error', 'Ticket sudah memiliki PIC.');
        }

        if ($ticket->status?->code !== 'OPEN') {
            return back()->with(
                'error',
                'Ticket hanya dapat diambil ketika status OPEN.'
            );
        }

        DB::transaction(function () use ($ticket, $user) {

            $oldAssignedTo = $ticket->assigned_to;

            $ticket->update([
                'assigned_to' => $user->id,
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'assigned',
                'old_value' => $oldAssignedTo,
                'new_value' => $user->id,
                'description' => 'Ticket diambil oleh ' . $user->name . '.',
            ]);
        });

        return back()->with(
            'success',
            'Ticket berhasil diambil.'
        );
    }

    public function start(Request $request, Ticket $ticket)
    {
        $user = $request->user();

        Gate::authorize('handle', $ticket);

        if ($ticket->assigned_to !== $user->id) {
            return back()->with(
                'error',
                'Ticket harus diambil oleh Anda terlebih dahulu.'
            );
        }

        if ($ticket->status?->code !== 'OPEN') {
            return back()->with(
                'error',
                'Ticket tidak berada pada status OPEN.'
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
            ]);

            $ticket->activities()->create([
                'user_id' => $user->id,
                'activity_type' => 'status_changed',
                'old_value' => $oldStatus,
                'new_value' => 'IN_PROGRESS',
                'description' => 'Penanganan ticket dimulai.',
            ]);
        });

        return back()->with(
            'success',
            'Penanganan ticket dimulai.'
        );
    }

    private function generateTicketNumber(): string
    {
        $year = now()->format('Y');

        $lastTicket = Ticket::query()
            ->whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $number = $lastTicket
            ? ((int) substr($lastTicket->ticket_number, -6)) + 1
            : 1;

        return sprintf(
            'IT-%s-%06d',
            $year,
            $number
        );
    }
}
