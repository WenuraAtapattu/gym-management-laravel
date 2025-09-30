<?php

namespace Database\Seeders;

use App\Models\FitnessClass;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FitnessClassSeeder extends Seeder
{
    public function run()
    {
        $classes = [
            [
                'name' => 'Morning CrossFit',
                'instructor_id' => 1,
                'description' => 'High-intensity functional training to start your day right!',
                'start_time' => '06:00:00',
                'end_time' => '07:00:00',
                'day_of_week' => 'monday',
                'capacity' => 15,
                'is_active' => true
            ],
            [
                'name' => 'Vinyasa Flow',
                'instructor_id' => 2,
                'description' => 'A dynamic sequence of yoga postures to build strength and flexibility.',
                'start_time' => '18:00:00',
                'end_time' => '19:00:00',
                'day_of_week' => 'monday',
                'capacity' => 20,
                'is_active' => true
            ],
            [
                'name' => 'HIIT Blast',
                'instructor_id' => 3,
                'description' => 'High-Intensity Interval Training to burn maximum calories.',
                'start_time' => '17:30:00',
                'end_time' => '18:15:00',
                'day_of_week' => 'tuesday',
                'capacity' => 12,
                'is_active' => true
            ],
            [
                'name' => 'Pilates Core',
                'instructor_id' => 4,
                'description' => 'Focus on core strength and stability through controlled movements.',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'day_of_week' => 'wednesday',
                'capacity' => 15,
                'is_active' => true
            ],
            [
                'name' => 'Strength Training',
                'instructor_id' => 5,
                'description' => 'Build muscle and strength with compound lifts and proper form.',
                'start_time' => '19:00:00',
                'end_time' => '20:00:00',
                'day_of_week' => 'thursday',
                'capacity' => 10,
                'is_active' => true
            ],
            [
                'name' => 'Weekend Yoga',
                'instructor_id' => 2,
                'description' => 'Gentle yoga flow to relax and restore over the weekend.',
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'day_of_week' => 'saturday',
                'capacity' => 20,
                'is_active' => true
            ]
        ];

        foreach ($classes as $class) {
            FitnessClass::create($class);
        }
    }
}
