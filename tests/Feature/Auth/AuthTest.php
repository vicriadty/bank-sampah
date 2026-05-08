<?php

namespace Tests\Feature\Auth;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature Test: Autentikasi (Login, Logout, Register)
 *
 * Menguji alur autentikasi pengguna:
 * - Skenario sukses login
 * - Login dengan kredensial salah
 * - Proteksi halaman login bagi user yang sudah login
 * - Alur registrasi (valid & invalid)
 * - Logout
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Halaman Login
    // -----------------------------------------------------------------------

    /** @test */
    public function guest_can_view_login_page(): void
    {
        // Act
        $response = $this->get('/login');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_is_redirected_back_from_login_page(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);

        // Act
        $response = $this->actingAs($user)->get('/login');

        // Assert
        $response->assertRedirect();
    }

    // -----------------------------------------------------------------------
    // Login
    // -----------------------------------------------------------------------

    /** @test */
    public function user_can_login_with_valid_credentials(): void
    {
        // Arrange
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Act
        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_fails_with_wrong_password(): void
    {
        // Arrange
        User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('correctpassword'),
        ]);

        // Act
        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        // Assert
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /** @test */
    public function login_fails_with_nonexistent_username(): void
    {
        // Act
        $response = $this->post('/login', [
            'username' => 'doesnotexist',
            'password' => 'anypassword',
        ]);

        // Assert
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /** @test */
    public function login_fails_when_username_is_empty(): void
    {
        // Act
        $response = $this->post('/login', [
            'username' => '',
            'password' => 'somepassword',
        ]);

        // Assert
        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function login_fails_when_password_is_empty(): void
    {
        // Act
        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => '',
        ]);

        // Assert
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function already_authenticated_user_is_redirected_on_post_login(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);

        // Act
        $response = $this->actingAs($user)->post('/login', [
            'username' => 'any',
            'password' => 'any',
        ]);

        // Assert
        $response->assertRedirect();
    }

    // -----------------------------------------------------------------------
    // Logout
    // -----------------------------------------------------------------------

    /** @test */
    public function authenticated_user_can_logout(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);

        // Act
        $response = $this->actingAs($user)->post('/logout');

        // Assert
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    // -----------------------------------------------------------------------
    // Halaman Register
    // -----------------------------------------------------------------------

    /** @test */
    public function guest_can_view_register_page(): void
    {
        // Act
        $response = $this->get('/register');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_is_redirected_from_register_page(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);

        // Act
        $response = $this->actingAs($user)->get('/register');

        // Assert
        $response->assertRedirect();
    }

    // -----------------------------------------------------------------------
    // Registrasi
    // -----------------------------------------------------------------------

    /** @test */
    public function user_can_register_with_valid_data(): void
    {
        // Act
        $response = $this->post('/register', [
            'username'              => 'newuser',
            'email'                 => 'new@banksampah.test',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $response->assertRedirect(route('register'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => 'new@banksampah.test']);
    }

    /** @test */
    public function registration_fails_when_email_is_already_taken(): void
    {
        // Arrange
        User::factory()->create(['email' => 'taken@banksampah.test']);

        // Act
        $response = $this->post('/register', [
            'username'              => 'anotheruser',
            'email'                 => 'taken@banksampah.test',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function registration_fails_when_password_too_short(): void
    {
        // Act
        $response = $this->post('/register', [
            'username'              => 'shortpwuser',
            'email'                 => 'short@banksampah.test',
            'password'              => '123',
            'password_confirmation' => '123',
        ]);

        // Assert
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_fails_when_password_confirmation_does_not_match(): void
    {
        // Act
        $response = $this->post('/register', [
            'username'              => 'mismatch',
            'email'                 => 'mismatch@banksampah.test',
            'password'              => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        // Assert
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_fails_with_invalid_email_format(): void
    {
        // Act
        $response = $this->post('/register', [
            'username'              => 'bademail',
            'email'                 => 'this-is-not-an-email',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function registration_fails_when_required_fields_are_empty(): void
    {
        // Act
        $response = $this->post('/register', []);

        // Assert
        $response->assertSessionHasErrors(['username', 'email', 'password']);
    }

    /** @test */
    public function password_is_stored_hashed_after_registration(): void
    {
        // Act
        $this->post('/register', [
            'username'              => 'hashtest',
            'email'                 => 'hash@banksampah.test',
            'password'              => 'mypassword',
            'password_confirmation' => 'mypassword',
        ]);

        // Assert
        $user = User::where('email', 'hash@banksampah.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('mypassword', $user->password));
    }
}
