<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'role' => 'student',
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '5551112233',
            'password' => 'password',
            'password_confirmation' => 'password',
            'accept_terms' => '1',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $this->assertNotNull(User::where('email', 'test@example.com')->first());
    }
}
