<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success_and_user_endpoint(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'admin_type' => 'system_admin',
        ]);

        $this->withSession([]);
        $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertOk();

        $this->getJson('/auth/user')->assertOk();
    }

    public function test_login_fail(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'admin_type' => 'system_admin',
        ]);

        $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpass',
        ])->assertStatus(422);
    }

    public function test_reset_flow(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'admin_type' => 'system_admin',
        ]);

        $this->postJson('/auth/forgot', ['email' => $user->email])
            ->assertOk();

        $user->refresh();
        $code = $user->reset_code;

        $this->postJson('/auth/reset', [
            'email' => $user->email,
            'code' => $code,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertOk();

        $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'newpassword123',
        ])->assertOk();
    }
}

