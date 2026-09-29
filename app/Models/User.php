<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_number',
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
        'phone',
        'position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array(
            $this->role?->name,
            $roles,
            true
        );
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function requestedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
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
        return $this->hasMany(TicketInspection::class, 'inspector_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(TicketAction::class, 'performed_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(TicketApproval::class, 'approver_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'supervisor_id'
        );
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(
            User::class,
            'supervisor_id'
        );
    }
}
