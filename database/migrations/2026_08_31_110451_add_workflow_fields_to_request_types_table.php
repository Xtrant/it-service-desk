<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_types', function (Blueprint $table) {
            $table->boolean('requires_approval')
                ->default(false)
                ->after('description');

            $table->string('approval_source', 30)
                ->nullable()
                ->after('requires_approval');

            $table->index([
                'is_active',
                'requires_approval',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('request_types', function (Blueprint $table) {
            $table->dropIndex([
                'is_active',
                'requires_approval',
            ]);

            $table->dropColumn([
                'requires_approval',
                'approval_source',
            ]);
        });
    }
};