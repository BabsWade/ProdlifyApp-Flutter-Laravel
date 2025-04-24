<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // Test de l'inscription
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => ['name', 'email', 'created_at']
                 ]);
    }

    // Test de la connexion
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    // Test de la vérification de l'email
    public function test_user_can_verify_email()
    {
        // Ici, nous supposerons que l'utilisateur est créé et un email est envoyé pour la vérification
        Mail::fake();

        $user = User::factory()->create(['email_verified_at' => null]);
        $token = \Str::random(60);

        $response = $this->getJson('/api/email/verify/'.$user->id.'?token='.$token);

        $response->assertStatus(200);
        Mail::assertSentTo($user, \App\Mail\VerifyEmail::class);
    }
}
