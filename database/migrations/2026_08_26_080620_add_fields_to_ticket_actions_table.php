<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_actions', function (Blueprint $table) {
            $table->foreignId('ticket_id')
                ->after('id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            $table->string('action_type', 50);

            $table->foreignId('performed_by')
                ->after('action_type')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('description')->nullable();

            $table->dateTime('performed_at');

            $table->index(['ticket_id', 'performed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_actions', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropForeign(['performed_by']);

            $table->dropColumn([
                'ticket_id',
                'action_type',
                'performed_by',
                'description',
                'performed_at',
            ]);
        });
    }
};