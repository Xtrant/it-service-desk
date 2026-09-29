<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Karyawan yang dapat membuat dan memantau ticket miliknya.',
            ],
            [
                'name' => 'it_staff',
                'display_name' => 'IT Staff',
                'description' => 'Petugas IT yang dapat menangani seluruh ticket.',
            ],
            [
                'name' => 'mis_supervisor',
                'display_name' => 'MIS Supervisor',
                'description' => 'Supervisor yang melakukan pemeriksaan, approval, monitoring, dan reporting.',
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrator sistem.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}