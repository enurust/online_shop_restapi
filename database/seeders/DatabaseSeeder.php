<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

         User::factory()->create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'balance' => '100000'

        ]);

         User::factory()->create([
            'name' => 'user2',
            'email' => 'user2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'balance' => '1000'
            
        ]);

        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
    }
}
