<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_comments', function (Blueprint $table) {
            $table->foreignId('ticket_id')
                ->after('id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->after('ticket_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('comment');

            $table->boolean('is_internal')
                ->default(false);

            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_comments', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'ticket_id',
                'user_id',
                'comment',
                'is_internal',
            ]);
        });
    }
};