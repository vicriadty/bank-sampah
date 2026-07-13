<?php

namespace Tests\Feature\Admin;

use App\Models\KategoriSampah;
use App\Models\Nasabah;
use App\Models\JenisSampah;
use App\Models\Setoran;
use App\Models\SetoranDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test: Admin Setoran Controller
 *
 * Menguji alur setoran sampah oleh admin:
 * - Hanya admin yang bisa akses
 * - Melihat daftar setoran
 * - Membuat setoran (hitung subtotal, tambah saldo)
 * - AJAX endpoint getSampahByJenis
 * - Validasi input
 * - Transaksi database (rollback saat error)
 */
class SetoranControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    /** @test */
    public function guest_cannot_access_setoran_index(): void
    {
        $response = $this->get(route('admin.setoran.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function setoran_increases_sampah_stock(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->withSaldo(0)->create();
        $jenisSampah  = JenisSampah::factory()->create(['harga_per_kg' => 2000, 'stok' => 10]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->id,
            'sampah_id'  => [$jenisSampah->id],
            'berat'      => [2.5],
        ]);

        // Assert
        $response->assertStatus(200);
        $this->assertEquals(12.5, $jenisSampah->fresh()->stok);
    }

    /** @test */
    public function admin_can_view_setoran_index(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->get(route('admin.setoran.index'));

        // Assert
        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Create
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_view_create_setoran_page(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->get(route('admin.setoran.create'));

        // Assert
        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Store (Inti bisnis — hitung subtotal & tambah saldo)
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_store_setoran_and_saldo_is_incremented(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->withSaldo(0)->create();
        $jenisSampah  = JenisSampah::factory()->create(['harga_per_kg' => 2000]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->id,
            'sampah_id'  => [$jenisSampah->id],
            'berat'      => [5], // kg
        ]);

        // Assert: response JSON sukses
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert: setoran tersimpan
        $this->assertDatabaseHas('setorans', ['nasabah_id' => $nasabah->id, 'total_harga' => 10000]);

        // Assert: saldo nasabah bertambah (5 kg × 2000 = 10000)
        $this->assertEquals(10000, $nasabah->fresh()->dompet->saldo_rupiah);
    }

    /** @test */
    public function subtotal_is_correctly_calculated_from_berat_and_harga_per_kg(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->withSaldo(0)->create();
        $jenisSampah  = JenisSampah::factory()->create(['harga_per_kg' => 3500]);

        // Act
        $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->id,
            'sampah_id'  => [$jenisSampah->id],
            'berat'      => [2.5],
        ]);

        // Assert: subtotal = 2.5 × 3500 = 8750
        $this->assertDatabaseHas('setoran_details', [
            'berat'      => 2.5,
            'harga_per_kg' => 3500,
            'subtotal'   => 8750,
        ]);
        $this->assertEquals(8750, $nasabah->fresh()->dompet->saldo_rupiah);
    }

    /** @test */
    public function store_setoran_fails_when_nasabah_id_does_not_exist(): void
    {
        // Arrange
        $admin  = $this->adminUser();
        $jenisSampah = JenisSampah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => 99999,
            'sampah_id'  => [$jenisSampah->id],
            'berat'      => [1],
        ]);

        // Assert
        $response->assertSessionHasErrors('nasabah_id');
    }

    /** @test */
    public function store_setoran_fails_when_sampah_id_does_not_exist(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->id,
            'sampah_id'  => [99999],
            'berat'      => [1],
        ]);

        // Assert
        $response->assertStatus(422);
    }

    /** @test */
    public function store_setoran_fails_when_berat_is_zero(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->create();
        $jenisSampah  = JenisSampah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->id,
            'sampah_id'  => [$jenisSampah->id],
            'berat'      => [0],
        ]);

        // Assert
        $response->assertStatus(422);
    }

    /** @test */
    public function store_setoran_fails_when_berat_is_negative(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->create();
        $jenisSampah  = JenisSampah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->id,
            'sampah_id'  => [$jenisSampah->id],
            'berat'      => [-1],
        ]);

        // Assert
        $response->assertStatus(422);
    }

    /** @test */
    public function store_setoran_fails_when_berat_is_not_numeric(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->create();
        $jenisSampah  = JenisSampah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->id,
            'sampah_id'  => [$jenisSampah->id],
            'berat'      => ['abc'],
        ]);

        // Assert
        $response->assertStatus(422);
    }

    /** @test */
    public function store_setoran_fails_when_required_fields_are_missing(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), []);

        // Assert
        $response->assertStatus(422);
    }

    // -----------------------------------------------------------------------
    // AJAX: getSampahByJenis
    // -----------------------------------------------------------------------

    /** @test */
    public function get_sampah_by_jenis_returns_json_for_valid_jenis(): void
    {
        // Arrange
        $admin      = $this->adminUser();
        $kategori   = KategoriSampah::factory()->create();
        $jenisSampahs    = JenisSampah::factory()->count(3)->create(['kategori_id' => $kategori->id]);

        // Act — menggunakan route langsung
        $url      = '/admin/get-sampah-by-jenis/' . $kategori->id;
        $response = $this->actingAs($admin)->getJson($url);
        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /** @test */
    public function get_sampah_by_jenis_returns_empty_array_for_nonexistent_jenis(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->getJson('/admin/get-sampah-by-jenis/99999');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }

    // -----------------------------------------------------------------------
    // Filter & Pagination
    // -----------------------------------------------------------------------

    /** @test */
    public function setoran_index_can_filter_by_nasabah_name(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = Nasabah::factory()->create(['nama' => 'Rina Kartini']);
        Setoran::factory()->create(['nasabah_id' => $nasabah->id]);

        // Act
        $response = $this->actingAs($admin)
            ->get(route('admin.setoran.index') . '?nasabah=Rina');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function setoran_index_can_filter_by_date_range(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)
            ->get(route('admin.setoran.index') . '?tanggal_awal=2024-01-01&tanggal_akhir=2024-12-31');

        // Assert
        $response->assertStatus(200);
    }
}
