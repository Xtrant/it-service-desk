<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $itRole = Role::where('name', 'it_staff')->firstOrFail();
        $employeeRole = Role::where('name', 'employee')->firstOrFail();

        $itDepartment = Department::where('code', 'IT')->firstOrFail();
        $financeDepartment = Department::where('code', 'FIN')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin@it-service-desk.test'],
            [
                'employee_number' => 'DEV-ADMIN',
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'department_id' => $itDepartment->id,
                'position' => 'System Administrator',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'it@it-service-desk.test'],
            [
                'employee_number' => 'DEV-IT-001',
                'name' => 'IT Staff',
                'password' => Hash::make('password'),
                'role_id' => $itRole->id,
                'department_id' => $itDepartment->id,
                'position' => 'IT Staff',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'employee@it-service-desk.test'],
            [
                'employee_number' => 'DEV-EMP-001',
                'name' => 'Test Employee',
                'password' => Hash::make('password'),
                'role_id' => $employeeRole->id,
                'department_id' => $financeDepartment->id,
                'position' => 'Employee',
                'is_active' => true,
            ]
        );
    }
}