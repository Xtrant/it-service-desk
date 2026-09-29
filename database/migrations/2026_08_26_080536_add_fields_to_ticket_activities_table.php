<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_activities', function (Blueprint $table) {
            $table->foreignId('ticket_id')
                ->after('id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->after('ticket_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('activity_type', 50);

            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('description')->nullable();

            $table->index(['ticket_id', 'created_at']);
            $table->index(['activity_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_activities', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'ticket_id',
                'user_id',
                'activity_type',
                'old_value',
                'new_value',
                'description',
            ]);
        });
    }
};