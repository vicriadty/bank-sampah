<?php

namespace Tests\Unit\Models;

use App\Models\Nasabah;
use App\Models\Setoran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Test: Nasabah Model
 *
 * Menguji fungsionalitas model Nasabah:
 * - Atribut fillable
 * - Relasi belongsTo User
 * - Relasi hasMany Setoran
 * - Logika saldo (increment/decrement)
 */
class NasabahModelTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Atribut
    // -----------------------------------------------------------------------

    /** @test */
    public function it_uses_correct_table_name(): void
    {
        $nasabah = new Nasabah();
        $this->assertEquals('nasabahs', $nasabah->getTable());
    }

    /** @test */
    public function it_has_correct_fillable_attributes(): void
    {
        $nasabah = new Nasabah();

        $this->assertContains('nik', $nasabah->getFillable());
        $this->assertContains('nama', $nasabah->getFillable());
        $this->assertContains('user_id', $nasabah->getFillable());
    }

    // -----------------------------------------------------------------------
    // Relasi
    // -----------------------------------------------------------------------

    /** @test */
    public function it_belongs_to_user(): void
    {
        // Arrange
        $user    = User::factory()->create(['role' => 'nasabah']);
        $nasabah = Nasabah::factory()->create(['user_id' => $user->id]);

        // Act & Assert
        $this->assertInstanceOf(User::class, $nasabah->user);
        $this->assertEquals($user->id, $nasabah->user->id);
    }

    /** @test */
    public function it_has_many_setorans(): void
    {
        // Arrange
        $nasabah = Nasabah::factory()->create();

        Setoran::factory()->count(3)->create(['nasabah_id' => $nasabah->id]);

        // Act & Assert
        $this->assertCount(3, $nasabah->setorans);
        $this->assertInstanceOf(Setoran::class, $nasabah->setorans->first());
    }

    // -----------------------------------------------------------------------
    // Logika Saldo
    // -----------------------------------------------------------------------

    /** @test */
    public function it_starts_with_zero_saldo_by_default(): void
    {
        // Arrange & Act
        $nasabah = Nasabah::factory()->create(['saldo' => 0]);

        // Assert
        $this->assertEquals(0, $nasabah->saldo);
    }

    /** @test */
    public function saldo_can_be_incremented(): void
    {
        // Arrange
        $nasabah = Nasabah::factory()->withSaldo(50000)->create();

        // Act
        $nasabah->increment('saldo', 25000);

        // Assert
        $this->assertEquals(75000, $nasabah->fresh()->saldo);
    }

    /** @test */
    public function saldo_can_be_decremented(): void
    {
        // Arrange
        $nasabah = Nasabah::factory()->withSaldo(100000)->create();

        // Act
        $nasabah->decrement('saldo', 40000);

        // Assert
        $this->assertEquals(60000, $nasabah->fresh()->saldo);
    }

    // -----------------------------------------------------------------------
    // CRUD
    // -----------------------------------------------------------------------

    /** @test */
    public function it_can_be_created_with_valid_data(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'nasabah']);

        // Act
        $nasabah = Nasabah::create([
            'user_id'       => $user->id,
            'nik'           => '1234567890123456',
            'nama'          => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '1990-01-01',
            'tempat_lahir'  => 'Jakarta',
            'alamat'        => 'Jl. Merdeka No. 1',
            'no_hp'         => '08123456789',
        ]);

        // Assert
        $this->assertDatabaseHas('nasabahs', ['nik' => '1234567890123456', 'nama' => 'Budi Santoso']);
    }

    /** @test */
    public function it_can_be_updated(): void
    {
        // Arrange
        $nasabah = Nasabah::factory()->create(['nama' => 'Lama']);

        // Act
        $nasabah->update(['nama' => 'Baru']);

        // Assert
        $this->assertDatabaseHas('nasabahs', ['id' => $nasabah->id, 'nama' => 'Baru']);
    }

    /** @test */
    public function it_can_be_soft_deleted_or_hard_deleted(): void
    {
        // Arrange
        $nasabah = Nasabah::factory()->create();
        $id      = $nasabah->id;

        // Act
        $nasabah->delete();

        // Assert
        $this->assertDatabaseMissing('nasabahs', ['id' => $id]);
    }
}
