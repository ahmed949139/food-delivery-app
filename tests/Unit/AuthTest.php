<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_user_registration_creates_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@email.com',
            'role' => 'customer',
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    #[Test]
    public function test_user_can_login_with_correct_password()
    {
        $user = User::create([
            'name' => 'Login User',
            'email' => 'login@email.com',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
        ]);

        $credentials = [
            'email' => 'login@email.com',
            'password' => 'secret123',
        ];

        $this->assertTrue(Auth::attempt($credentials));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function test_user_cannot_login_with_wrong_password()
    {
        $user = User::create([
            'name' => 'Wrong Password User',
            'email' => 'wrongpass@email.com',
            'password' => Hash::make('rightpassword'),
            'role' => 'customer',
        ]);

        $credentials = [
            'email' => 'wrongpass@email.com',
            'password' => 'wrongpassword',
        ];

        $this->assertFalse(Auth::attempt($credentials));
        $this->assertGuest();
    }
}
