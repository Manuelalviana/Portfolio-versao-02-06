<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
                IconSeeder::class,
            ]);
            
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Admin de desenvolvimento
        User::factory()->create([
            'name' => 'Admin Fiocruz',
            'email' => 'admin@fiocruz.br',
            'is_admin' => true,
        ]);
    }
}