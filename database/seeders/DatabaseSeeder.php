<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::factory()->create([
            'first_name' => 'Usuario',
            'last_name' => 'Demo',
            'email' => 'demo@contactapi.test',
        ]);

        Contact::factory(10)->for($demo)->create();

        $prueba = User::factory()->create([
            'first_name' => 'Usuario',
            'last_name' => 'Prueba',
            'email' => 'prueba@test.com',
        ]);

        Contact::factory(5)->for($prueba)->create();

        User::factory(2)
            ->has(Contact::factory(5))
            ->create();
    }
}
