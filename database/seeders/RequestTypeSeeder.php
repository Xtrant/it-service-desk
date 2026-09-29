<?php

namespace Database\Seeders;

use App\Models\RequestType;
use Illuminate\Database\Seeder;

class RequestTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // FORM PERMINTAAN
            [
                'code' => 'HARDWARE_REPAIR',
                'name' => 'Perbaikan Hardware',
                'description' => 'Permintaan perbaikan perangkat keras.',
                'requires_approval' => true,
                'approval_source' => 'SUPERVISOR',
            ],
            [
                'code' => 'HARDWARE_REPLACEMENT',
                'name' => 'Penggantian Hardware',
                'description' => 'Permintaan penggantian perangkat keras.',
                'requires_approval' => true,
                'approval_source' => 'SUPERVISOR',
            ],
            [
                'code' => 'SOFTWARE',
                'name' => 'Permintaan Software',
                'description' => 'Permintaan instalasi atau penggunaan software.',
                'requires_approval' => true,
                'approval_source' => 'SUPERVISOR',
            ],
            [
                'code' => 'APPLICATION',
                'name' => 'Pembuatan / Pengembangan Aplikasi',
                'description' => 'Permintaan pembuatan atau pengembangan aplikasi.',
                'requires_approval' => true,
                'approval_source' => 'SUPERVISOR',
            ],
            [
                'code' => 'DESIGN',
                'name' => 'Pembuatan Graphic Design',
                'description' => 'Permintaan pembuatan grafik atau desain.',
                'requires_approval' => true,
                'approval_source' => 'SUPERVISOR',
            ],

            // FORM EMAIL
            [
                'code' => 'EMAIL_REQUEST',
                'name' => 'Permintaan Email',
                'description' => 'Permintaan pembuatan atau pengaturan akun email.',
                'requires_approval' => true,
                'approval_source' => 'SUPERVISOR',
            ],
            [
                'code' => 'EMAIL_RELEASE',
                'name' => 'Pengeluaran Email',
                'description' => 'Permintaan pengeluaran atau pelepasan akun email.',
                'requires_approval' => true,
                'approval_source' => 'SUPERVISOR',
            ],
            [
                'code' => 'EMAIL_CLOSURE',
                'name' => 'Penutupan Email',
                'description' => 'Permintaan penutupan akun email.',
                'requires_approval' => true,
                'approval_source' => 'SUPERVISOR',
            ],

            // FORM MAINTENANCE
            [
                'code' => 'MAINTENANCE_ROUTINE',
                'name' => 'Maintenance Rutin MIS',
                'description' => 'Pelaksanaan maintenance rutin oleh MIS.',
                'requires_approval' => false,
                'approval_source' => null,
            ],
        ];

        foreach ($types as $type) {
            RequestType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}