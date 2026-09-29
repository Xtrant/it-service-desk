<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('ticket_type', 20)
                ->default('INCIDENT')
                ->after('ticket_number');

            $table->index([
                'ticket_type',
                'status_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex([
                'ticket_type',
                'status_id',
            ]);

            $table->dropColumn('ticket_type');
        });
    }
};