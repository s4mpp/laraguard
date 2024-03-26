<?php

namespace S4mpp\Laraguard\Tests\Feature;

use Exception;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Workbench\Database\Factories\UserFactory;

final class ChangePersonalDataTest extends TestCase
{
    public function test_index_page(): void
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user, 'web')->get('/area-restrita/minha-conta/meus-dados');

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    public function test_change_personal_data(): void
    {
        $current_password = 'passw0rd';

        $old_data = ['name' => fake()->name(), 'email' => fake()->safeEmail()];
        $new_data = ['name' => fake()->name(), 'email' => fake()->safeEmail()];

        $user = UserFactory::new([
            'name' => $old_data['name'],
            'email' => $old_data['email'],
            'password' => Hash::make($current_password),
        ])->create();

        $this->get('area-restrita/minha-conta/meus-dados');
        $response = $this->actingAs($user, 'web')->put('area-restrita/minha-conta/meus-dados/salvar-dados', [
            'current_password' => $current_password,
            'name' => $new_data['name'],
            'email' => $new_data['email'],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('area-restrita/minha-conta/meus-dados');

        $this->assertDatabaseHas('users', [
            'name' => $new_data['name'],
            'email' => $new_data['email'],
        ]);

        $this->assertDatabaseMissing('users', [
            'name' => $old_data['name'],
            'email' => $old_data['email'],
        ]);
    }

    public function test_change_personal_data_with_invalid_password(): void
    {
        $old_data = ['name' => fake()->name(), 'email' => fake()->safeEmail()];
        $new_data = ['name' => fake()->name(), 'email' => fake()->safeEmail()];

        $user = UserFactory::new([
            'name' => $old_data['name'],
            'email' => $old_data['email'],
        ])->create();

        $this->get('area-restrita/minha-conta/meus-dados');
        $response = $this->actingAs($user, 'web')->put('area-restrita/minha-conta/meus-dados/salvar-dados', [
            'current_password' => 'another-pass123',
            'name' => $new_data['name'],
            'email' => $new_data['email'],
        ]);

        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('users', [
            'name' => $new_data['name'],
            'email' => $new_data['email'],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $old_data['name'],
            'email' => $old_data['email'],
        ]);
    }

}
