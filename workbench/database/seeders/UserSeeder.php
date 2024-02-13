<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Workbench\Database\Factories\UserFactory;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::new([
            'email' => 'user@email.com',
        ])->create();
    }
}
