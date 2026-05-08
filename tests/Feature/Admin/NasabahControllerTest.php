<?php

namespace Tests\Feature\Admin;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test: Admin Nasabah Controller
 *
 * Menguji CRUD nasabah oleh admin:
 * - Hanya admin yang bisa akses
 * - Membuat, melihat, mengubah, menghapus nasabah
 * - Validasi input
 * - Pencarian nasabah
 */
class NasabahControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helper: buat user admin
    // -----------------------------------------------------------------------

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function nasabahUser(): User
    {
        return User::factory()->create(['role' => 'nasabah']);
    }

    // -----------------------------------------------------------------------
    // Proteksi akses (role middleware)
    // -----------------------------------------------------------------------

    /** @test */
    public function guest_cannot_access_nasabah_index(): void
    {
        $response = $this->get(route('admin.nasabah.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function nasabah_role_cannot_access_nasabah_index(): void
    {
        $user     = $this->nasabahUser();
        $response = $this->actingAs($user)->get(route('admin.nasabah.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_nasabah_index(): void
    {
        $admin = $this->adminUser();
        Nasabah::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.nasabah.index'));
        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Buat Nasabah (store)
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_create_nasabah_with_valid_data(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.nasabah.store'), [
            'nik'           => '1234567890123456',
            'nama'          => 'Budi Santoso',
            'username'      => 'budisant',
            'email'         => 'budi@test.com',
            'password'      => 'password123',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '1990-05-15',
            'tempat_lahir'  => 'Jakarta',
            'alamat'        => 'Jl. Merdeka No. 1',
            'no_hp'         => '08123456789',
        ]);

        // Assert
        $response->assertRedirect(route('admin.nasabah.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('nasabahs', ['nama' => 'Budi Santoso']);
        $this->assertDatabaseHas('users', ['email' => 'budi@test.com', 'role' => 'nasabah']);
    }

    /** @test */
    public function creating_nasabah_also_creates_user_account(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $this->actingAs($admin)->post(route('admin.nasabah.store'), [
            'nik'           => '1234567890123456',
            'nama'          => 'Siti Aisyah',
            'username'      => 'sitiaisyah',
            'email'         => 'siti@test.com',
            'password'      => 'password123',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '1995-03-20',
            'tempat_lahir'  => 'Bandung',
            'alamat'        => 'Jl. Sudirman No. 5',
            'no_hp'         => '08198765432',
        ]);

        // Assert — keduanya harus tersimpan
        $user = User::where('email', 'siti@test.com')->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('nasabahs', ['user_id' => $user->id]);
    }

    /** @test */
    public function store_fails_when_email_is_already_taken(): void
    {
        // Arrange
        $admin = $this->adminUser();
        User::factory()->create(['email' => 'taken@test.com']);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.nasabah.store'), [
            'nik'           => '1234567890123456',
            'nama'          => 'Duplikat',
            'username'      => 'duplikat',
            'email'         => 'taken@test.com',
            'password'      => 'password123',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '1990-01-01',
            'tempat_lahir'  => 'Surabaya',
            'alamat'        => 'Jl. A No. 1',
            'no_hp'         => '081234567890',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function store_fails_when_required_fields_are_missing(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.nasabah.store'), []);

        // Assert
        $response->assertSessionHasErrors(['nik', 'nama', 'username', 'email', 'password', 'jenis_kelamin']);
    }

    /** @test */
    public function store_fails_when_jenis_kelamin_is_invalid(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.nasabah.store'), [
            'nik'           => '1234567890123456',
            'nama'          => 'Invalid Gender',
            'username'      => 'invgender',
            'email'         => 'inv@test.com',
            'password'      => 'password123',
            'jenis_kelamin' => 'INVALID_VALUE',
            'tanggal_lahir' => '1990-01-01',
            'tempat_lahir'  => 'Kota',
            'alamat'        => 'Jl. X',
            'no_hp'         => '081234567890',
        ]);

        // Assert
        $response->assertSessionHasErrors('jenis_kelamin');
    }

    // -----------------------------------------------------------------------
    // Edit & Update
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_view_edit_nasabah_page(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->get(route('admin.nasabah.edit', $nasabah->id));

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_nasabah(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->create(['nama' => 'Nama Lama']);

        // Act
        $response = $this->actingAs($admin)->put(route('admin.nasabah.update', $nasabah), [
            'nik'           => $nasabah->nik,
            'nama'          => 'Nama Baru',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => $nasabah->tanggal_lahir,
            'tempat_lahir'  => $nasabah->tempat_lahir,
            'alamat'        => $nasabah->alamat,
            'no_hp'         => $nasabah->no_hp,
        ]);

        // Assert
        $response->assertRedirect(route('admin.nasabah.index'));
        $this->assertDatabaseHas('nasabahs', ['id' => $nasabah->id, 'nama' => 'Nama Baru']);
    }

    /** @test */
    public function update_returns_404_for_nonexistent_nasabah(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->put(route('admin.nasabah.update', 99999), [
            'nik'           => '1234567890123456',
            'nama'          => 'X',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2000-01-01',
            'tempat_lahir'  => 'X',
            'alamat'        => 'X',
        ]);

        // Assert
        $response->assertStatus(404);
    }

    // -----------------------------------------------------------------------
    // Hapus (destroy)
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_delete_nasabah(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->delete(route('admin.nasabah.destroy', $nasabah->id));

        // Assert
        $response->assertRedirect(route('admin.nasabah.index'));
        $this->assertDatabaseMissing('nasabahs', ['id' => $nasabah->id]);
    }

    /** @test */
    public function destroy_returns_404_for_nonexistent_nasabah(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->delete(route('admin.nasabah.destroy', 99999));

        // Assert
        $response->assertStatus(404);
    }

    // -----------------------------------------------------------------------
    // Pencarian
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_search_nasabah_by_name(): void
    {
        // Arrange
        $admin = $this->adminUser();
        Nasabah::factory()->create(['nama' => 'Ahmad Fulan']);
        Nasabah::factory()->create(['nama' => 'Budi Santoso']);

        // Act
        $response = $this->actingAs($admin)->get(route('admin.nasabah.search') . '?nasabah=Ahmad');

        // Assert
        $response->assertStatus(200);
    }
}
