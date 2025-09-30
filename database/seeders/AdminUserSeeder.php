<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrNew(['email' => 'admin@example.com']);
        
        if ($admin->exists) {
            $admin->is_admin = true;
            $admin->save();
            $this->command->info('Existing admin user updated with admin privileges.');
        } else {
            $admin->fill([
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ])->save();
            $this->command->info('Admin user created successfully!');
        }
        
        $this->command->warn('Admin Credentials:');
        $this->command->warn('Email: admin@example.com');
        $this->command->warn('Password: password');
        $this->command->warn('IMPORTANT: Change this password after first login!');
    }
}
