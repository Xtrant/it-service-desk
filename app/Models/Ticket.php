<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'ticket_type',
        'requester_id',
        'department_id',
        'request_type_id',
        'asset_id',
        'status_id',
        'assigned_to',
        'priority',
        'title',
        'description',
        'details',
        'needed_at',
        'submitted_at',
        'resolved_at',
        'closed_at',
        'requester_signature_path',
    ];

    protected function casts(): array
    {
        return [
            'needed_at' => 'datetime',
            'submitted_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'details' => 'array',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function requestType(): BelongsTo
    {
        return $this->belongsTo(RequestType::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(TicketInspection::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(TicketAction::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(TicketApproval::class);
    }
}
