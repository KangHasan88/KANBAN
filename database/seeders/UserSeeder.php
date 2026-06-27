<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@kanban.com'],
            [
                'username' => 'admin',
                'name' => 'Administrator',
                'email' => 'admin@kanban.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );
        
        // John Doe (regular user)
        User::updateOrCreate(
            ['email' => 'john@example.com'],
            [
                'username' => 'john_doe',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );
        
        // Jane Smith (regular user)
        User::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'username' => 'jane_smith',
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );
        
        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('   - Admin: admin@kanban.com / password123 (role: admin)');
        $this->command->info('   - John Doe: john@example.com / password123 (role: user)');
        $this->command->info('   - Jane Smith: jane@example.com / password123 (role: user)');
    }
}