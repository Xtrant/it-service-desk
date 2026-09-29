<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('ticket_number', 30)
                ->unique()
                ->after('id');

            $table->foreignId('requester_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            $table->foreignId('request_type_id')
                ->constrained('request_types')
                ->restrictOnDelete();

            $table->foreignId('asset_id')
                ->nullable()
                ->constrained('assets')
                ->nullOnDelete();

            $table->foreignId('status_id')
                ->constrained('ticket_statuses')
                ->restrictOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('priority', 20)
                ->default('NORMAL');

            $table->string('title', 200);

            $table->text('description');

            $table->dateTime('needed_at')
                ->nullable();

            $table->dateTime('submitted_at')
                ->nullable();

            $table->dateTime('resolved_at')
                ->nullable();

            $table->dateTime('closed_at')
                ->nullable();

            $table->string('requester_signature_path', 255)
                ->nullable();

            $table->index(['status_id', 'assigned_to']);
            $table->index(['requester_id', 'created_at']);
            $table->index(['department_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['requester_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['request_type_id']);
            $table->dropForeign(['asset_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['assigned_to']);

            $table->dropUnique(['ticket_number']);

            $table->dropColumn([
                'ticket_number',
                'requester_id',
                'department_id',
                'request_type_id',
                'asset_id',
                'status_id',
                'assigned_to',
                'priority',
                'title',
                'description',
                'needed_at',
                'submitted_at',
                'resolved_at',
                'closed_at',
                'requester_signature_path',
            ]);
        });
    }
};