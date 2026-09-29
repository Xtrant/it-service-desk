<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_approvals', function (Blueprint $table) {
            $table->foreignId('ticket_id')
                ->after('id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            $table->foreignId('approver_id')
                ->after('ticket_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('approval_type', 50);

            $table->string('status', 30)
                ->default('PENDING');

            $table->text('notes')->nullable();

            $table->string('signature_path', 255)
                ->nullable();

            $table->dateTime('approved_at')
                ->nullable();

            $table->index(['ticket_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_approvals', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropForeign(['approver_id']);

            $table->dropColumn([
                'ticket_id',
                'approver_id',
                'approval_type',
                'status',
                'notes',
                'signature_path',
                'approved_at',
            ]);
        });
    }
};