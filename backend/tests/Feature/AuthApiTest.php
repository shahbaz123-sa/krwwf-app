<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_mobile_and_login_using_mobile(): void
    {
        $registerResponse = $this->postJson('/api/auth/register', [
            'name' => 'Demo User',
            'country_code' => '+880',
            'mobile_number' => '1712345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registerResponse
            ->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'mobile_number', 'email']])
            ->assertJsonPath('user.mobile_number', '+8801712345678')
            ->assertJsonPath('user.email', null);

        $loginResponse = $this->postJson('/api/auth/login', [
            'login_with' => 'mobile',
            'country_code' => '+880',
            'mobile_number' => '1712345678',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('mobile_number', '+8801712345678');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout')
            ->assertOk();
    }

    public function test_user_can_login_using_email_when_selected(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Email User',
            'country_code' => '+1',
            'mobile_number' => '5555555555',
            'email' => 'email-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $this->postJson('/api/auth/login', [
            'login_with' => 'email',
            'email' => 'email-user@example.com',
            'password' => 'password123',
        ])->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
    }

    public function test_authenticated_user_can_update_profile_details(): void
    {
        $register = $this->postJson('/api/auth/register', [
            'name' => 'Profile User',
            'country_code' => '+92',
            'mobile_number' => '3001234567',
            'email' => 'profile@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $token = $register->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/user', [
                'mobile_number' => '+923009999999',
                'email' => 'updated@example.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertOk()
            ->assertJsonPath('mobile_number', '+923009999999')
            ->assertJsonPath('email', 'updated@example.com');

        $this->postJson('/api/auth/login', [
            'login_with' => 'email',
            'email' => 'updated@example.com',
            'password' => 'newpassword123',
        ])->assertOk();
    }
}

