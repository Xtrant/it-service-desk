<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_inspections', function (Blueprint $table) {
            $table->foreignId('ticket_id')
                ->after('id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            $table->foreignId('inspector_id')
                ->after('ticket_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('result', 100);
            $table->text('notes')->nullable();

            $table->string('signature_path', 255)
                ->nullable();

            $table->dateTime('inspected_at');

            $table->index(['ticket_id', 'inspected_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_inspections', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropForeign(['inspector_id']);

            $table->dropColumn([
                'ticket_id',
                'inspector_id',
                'result',
                'notes',
                'signature_path',
                'inspected_at',
            ]);
        });
    }
};