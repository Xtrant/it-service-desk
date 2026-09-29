<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_number', 50)
                ->nullable()
                ->unique()
                ->after('id');

            $table->foreignId('role_id')
                ->nullable()
                ->after('password')
                ->constrained('roles')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->after('role_id')
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('phone', 30)
                ->nullable()
                ->after('department_id');

            $table->string('position', 100)
                ->nullable()
                ->after('phone');

            $table->boolean('is_active')
                ->default(true)
                ->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['department_id']);

            $table->dropColumn([
                'employee_number',
                'role_id',
                'department_id',
                'phone',
                'position',
                'is_active',
            ]);
        });
    }
};