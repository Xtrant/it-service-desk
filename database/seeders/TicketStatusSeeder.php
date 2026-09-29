<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'code' => 'OPEN',
                'name' => 'Open',
                'sort_order' => 1,
                'is_closed' => false,
            ],
            [
                'code' => 'IN_PROGRESS',
                'name' => 'In Progress',
                'sort_order' => 2,
                'is_closed' => false,
            ],
            [
                'code' => 'WAITING_APPROVAL',
                'name' => 'Waiting Approval',
                'sort_order' => 3,
                'is_closed' => false,
            ],
            [
                'code' => 'WAITING_USER',
                'name' => 'Waiting User',
                'sort_order' => 4,
                'is_closed' => false,
            ],
            [
                'code' => 'RESOLVED',
                'name' => 'Resolved',
                'sort_order' => 5,
                'is_closed' => false,
            ],
            [
                'code' => 'CLOSED',
                'name' => 'Closed',
                'sort_order' => 6,
                'is_closed' => true,
            ],
            [
                'code' => 'CANCELLED',
                'name' => 'Cancelled',
                'sort_order' => 7,
                'is_closed' => true,
            ],
        ];

        foreach ($statuses as $status) {
            TicketStatus::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }
}