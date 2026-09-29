<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'inspector_id',
        'result',
        'notes',
        'signature_path',
        'inspected_at',
    ];

    protected function casts(): array
    {
        return [
            'inspected_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'inspector_id'
        );
    }
}   