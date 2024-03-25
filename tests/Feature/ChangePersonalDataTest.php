<?php

namespace S4mpp\Laraguard\Tests\Feature;

use Exception;
use S4mpp\Laraguard\Tests\TestCase;
use Illuminate\Support\Facades\Hash;

final class ChangePersonalDataTest extends TestCase
{
    /**
     * @dataProvider panelProvider
     */
    public function test_change_personal_data($panel): void
    {
        $current_password = 'passw0rd';

        $old_data = ['name' => fake()->name(), 'email' => fake()->safeEmail()];
        $new_data = ['name' => fake()->name(), 'email' => fake()->safeEmail()];

        $factory = $panel['factory'];
        $user = $factory::new([
            'name' => $old_data['name'],
            'email' => $old_data['email'],
            'password' => Hash::make($current_password),
        ])->create();

        $this->get($panel['prefix'].'/minha-conta');
        $response = $this->actingAs($user, $panel['guard_name'])->put($panel['prefix'].'/minha-conta/salvar-dados-pessoais', [
            'current_password' => $current_password,
            'name' => $new_data['name'],
            'email' => $new_data['email'],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect($panel['prefix'].'/minha-conta');

        $table = app($factory::new()->modelName())->getTable();

        $this->assertDatabaseHas($table, [
            'name' => $new_data['name'],
            'email' => $new_data['email'],
        ]);

        $this->assertDatabaseMissing($table, [
            'name' => $old_data['name'],
            'email' => $old_data['email'],
        ]);
    }

    /**
     * @dataProvider panelProvider
     */
    public function test_change_personal_data_with_invalid_password($panel): void
    {
        $old_data = ['name' => fake()->name(), 'email' => fake()->safeEmail()];
        $new_data = ['name' => fake()->name(), 'email' => fake()->safeEmail()];

        $factory = $panel['factory'];
        $user = $factory::new([
            'name' => $old_data['name'],
            'email' => $old_data['email'],
        ])->create();

        $this->get($panel['prefix'].'/minha-conta');
        $response = $this->actingAs($user, $panel['guard_name'])->put($panel['prefix'].'/minha-conta/salvar-dados-pessoais', [
            'current_password' => 'another-pass123',
            'name' => $new_data['name'],
            'email' => $new_data['email'],
        ]);

        $response->assertSessionHasErrors();

        $table = app($factory::new()->modelName())->getTable();

        $this->assertDatabaseMissing($table, [
            'name' => $new_data['name'],
            'email' => $new_data['email'],
        ]);

        $this->assertDatabaseHas($table, [
            'name' => $old_data['name'],
            'email' => $old_data['email'],
        ]);
    }

}
