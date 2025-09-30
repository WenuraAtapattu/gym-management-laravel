<?php

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run()
    {
        $memberships = [
            [
                'user_id' => 1, // Assuming admin user with ID 1 exists
                'type' => 'basic',
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'status' => 'active',
                'price' => 29.99,
                'payment_status' => 'paid',
                'notes' => 'Basic membership with limited access to facilities',
            ],
            [
                'user_id' => 1, // Assuming admin user with ID 1 exists
                'type' => 'premium',
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'status' => 'active',
                'price' => 59.99,
                'payment_status' => 'paid',
                'notes' => 'Premium membership with full access to all facilities',
            ],
            [
                'user_id' => 1, // Assuming admin user with ID 1 exists
                'type' => 'student',
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'status' => 'active',
                'price' => 19.99,
                'payment_status' => 'paid',
                'notes' => 'Discounted membership for students with valid ID',
            ],
        ];

        foreach ($memberships as $membership) {
            Membership::create($membership);
        }
    }
}
