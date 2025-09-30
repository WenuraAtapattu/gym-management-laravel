<?php

namespace Database\Seeders;

use App\Models\Instructor;
use Illuminate\Database\Seeder;

class InstructorSeeder extends Seeder
{
    public function run()
    {
        $instructors = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
                'specialty' => 'CrossFit',
                'bio' => 'Certified CrossFit trainer with 10+ years of experience.',
                'is_active' => true
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '2345678901',
                'specialty' => 'Yoga',
                'bio' => 'RYT-500 certified yoga instructor specializing in Vinyasa and Hatha.',
                'is_active' => true
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'phone' => '3456789012',
                'specialty' => 'HIIT',
                'bio' => 'HIIT specialist helping clients achieve their fitness goals through high-intensity training.',
                'is_active' => true
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah@example.com',
                'phone' => '4567890123',
                'specialty' => 'Pilates',
                'bio' => 'Pilates instructor focused on core strength and flexibility.',
                'is_active' => true
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'phone' => '5678901234',
                'specialty' => 'Weight Training',
                'bio' => 'Strength and conditioning coach with a focus on proper form and technique.',
                'is_active' => true
            ]
        ];

        foreach ($instructors as $instructor) {
            Instructor::create($instructor);
        }
    }
}
