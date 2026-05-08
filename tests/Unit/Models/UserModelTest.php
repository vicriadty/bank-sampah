<?php

namespace Tests\Unit\Models;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Test: User Model
 *
 * Menguji fungsionalitas model User:
 * - Atribut fillable & hidden
 * - Relasi hasOne ke Nasabah
 * - Password di-hash secara otomatis
 */
class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Atribut Fillable & Hidden
    // -----------------------------------------------------------------------

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        // Arrange
        $user = new User();

        // Assert
        $this->assertEquals(
            ['username', 'email', 'password', 'role'],
            $user->getFillable()
        );
    }

    /** @test */
    public function it_has_correct_hidden_attributes(): void
    {
        // Arrange
        $user = new User();

        // Assert
        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }

    /** @test */
    public function password_is_hashed_automatically(): void
    {
        // Arrange & Act
        $user = User::factory()->create(['password' => 'plaintext_password']);

        // Assert — password tidak boleh disimpan sebagai plaintext
        $this->assertNotEquals('plaintext_password', $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('plaintext_password', $user->password));
    }

    // -----------------------------------------------------------------------
    // Relasi
    // -----------------------------------------------------------------------

    /** @test */
    public function it_has_one_nasabah_relation(): void
    {
        // Arrange
        $user    = User::factory()->create(['role' => 'nasabah']);
        $nasabah = Nasabah::factory()->create(['user_id' => $user->id]);

        // Act
        $related = $user->nasabah;

        // Assert
        $this->assertInstanceOf(Nasabah::class, $related);
        $this->assertEquals($nasabah->id, $related->id);
    }

    /** @test */
    public function nasabah_relation_returns_null_when_no_nasabah_exists(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);

        // Act & Assert
        $this->assertNull($user->nasabah);
    }

    // -----------------------------------------------------------------------
    // Pembuatan User
    // -----------------------------------------------------------------------

    /** @test */
    public function it_can_create_admin_user(): void
    {
        // Arrange & Act
        $user = User::factory()->create([
            'username' => 'admin01',
            'email'    => 'admin@banksampah.test',
            'role'     => 'admin',
        ]);

        // Assert
        $this->assertDatabaseHas('users', [
            'username' => 'admin01',
            'email'    => 'admin@banksampah.test',
            'role'     => 'admin',
        ]);
    }

    /** @test */
    public function it_can_create_nasabah_user(): void
    {
        // Arrange & Act
        $user = User::factory()->create(['role' => 'nasabah']);

        // Assert
        $this->assertEquals('nasabah', $user->role);
    }
}
