<?php

namespace Tests\Feature\Admin;

use App\Models\Pengepul;
use App\Models\PenjualanSampah;
use App\Models\JenisSampah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanSampahControllerTest extends TestCase
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
    public function admin_can_view_penjualan_index(): void
    {
        $admin    = $this->adminUser();
        $response = $this->actingAs($admin)->get(route('admin.penjualan.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_access_penjualan_index(): void
    {
        $response = $this->get(route('admin.penjualan.index'));
        $response->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // Create
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_view_create_penjualan_page(): void
    {
        $admin    = $this->adminUser();
        $response = $this->actingAs($admin)->get(route('admin.penjualan.create'));
        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Store — Skenario Sukses
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_store_penjualan_with_single_item(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();
        $jenisSampah   = JenisSampah::factory()->create(['harga_per_kg' => 1000]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => [$jenisSampah->id],
            'berat'       => [10],
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // total_harga = 10 × 1000 = 10000
        $this->assertDatabaseHas('penjualan_sampahs', [
            'pengepul_id' => $pengepul->id,
            'total_harga' => 10000,
        ]);
    }

    /** @test */
    public function admin_can_store_penjualan_with_multiple_items(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();
        $jenisSampah1  = JenisSampah::factory()->create(['harga_per_kg' => 2000]);
        $jenisSampah2  = JenisSampah::factory()->create(['harga_per_kg' => 3000]);

        // Act
        $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => [$jenisSampah1->id, $jenisSampah2->id],
            'berat'       => [5, 3],
        ]);

        // Assert
        $this->assertDatabaseHas('penjualan_sampahs', [
            'pengepul_id' => $pengepul->id,
            'total_harga' => 19000,
        ]);

        // Detail tersimpan
        $this->assertDatabaseHas('detail_penjualan_sampahs', ['sampah_id' => $jenisSampah1->id, 'berat' => 5, 'subtotal' => 10000]);
        $this->assertDatabaseHas('detail_penjualan_sampahs', ['sampah_id' => $jenisSampah2->id, 'berat' => 3, 'subtotal' => 9000]);
    }

    // -----------------------------------------------------------------------
    // Store — Stock behavior
    // -----------------------------------------------------------------------

    /** @test */
    public function penjualan_decreases_sampah_stock(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();
        $jenisSampah   = JenisSampah::factory()->create(['harga_per_kg' => 1000, 'stok' => 10]);

        // Act
        $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => [$jenisSampah->id],
            'berat'       => [3],
        ]);

        // Assert: stock harus berkurang
        $this->assertEquals(7, $jenisSampah->fresh()->stok);
    }

    /** @test */
    public function penjualan_with_zero_berat_fails_validation(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();
        $jenisSampah   = JenisSampah::factory()->create(['harga_per_kg' => 1000, 'stok' => 10]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => [$jenisSampah->id],
            'berat'       => [0],
        ]);

        // Assert
        $response->assertStatus(422);
        $this->assertDatabaseCount('penjualan_sampahs', 0);
        $this->assertDatabaseCount('detail_penjualan_sampahs', 0);
        $this->assertEquals(10, $jenisSampah->fresh()->stok);
    }

    /** @test */
    public function penjualan_with_insufficient_stock_fails_validation(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();
        $jenisSampah   = JenisSampah::factory()->create(['harga_per_kg' => 1000, 'stok' => 10]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => [$jenisSampah->id],
            'berat'       => [11],
        ]);

        // Assert
        $response->assertStatus(422);
        $this->assertDatabaseCount('penjualan_sampahs', 0);
        $this->assertDatabaseCount('detail_penjualan_sampahs', 0);
        $this->assertEquals(10, $jenisSampah->fresh()->stok);
    }

    /** @test */
    public function penjualan_rolls_back_all_changes_when_one_item_exceeds_stock(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();
        $jenisSampah1  = JenisSampah::factory()->create(['harga_per_kg' => 2000, 'stok' => 10]);
        $jenisSampah2  = JenisSampah::factory()->create(['harga_per_kg' => 3000, 'stok' => 5]);

        // Act: item 1 valid (5 < 10), item 2 melebihi stok (10 > 5)
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => [$jenisSampah1->id, $jenisSampah2->id],
            'berat'       => [5, 10],
        ]);

        // Assert: validation error
        $response->assertStatus(422);

        // Tidak ada satupun penjualan atau detail yang tersimpan
        $this->assertDatabaseCount('penjualan_sampahs', 0);
        $this->assertDatabaseCount('detail_penjualan_sampahs', 0);

        // Stock kedua sampah tetap tidak berubah
        $this->assertEquals(10, $jenisSampah1->fresh()->stok);
        $this->assertEquals(5, $jenisSampah2->fresh()->stok);
    }

    // -----------------------------------------------------------------------
    // Store — Validasi
    // -----------------------------------------------------------------------

    /** @test */
    public function store_fails_when_pengepul_id_does_not_exist(): void
    {
        // Arrange
        $admin  = $this->adminUser();
        $jenisSampah = JenisSampah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => 99999,
            'sampah_id'   => [$jenisSampah->id],
            'berat'       => [1],
        ]);

        // Assert
        $response->assertSessionHasErrors('pengepul_id');
    }

    /** @test */
    public function store_fails_when_sampah_id_is_not_an_array(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => 'bukan_array',
            'berat'       => [1],
        ]);

        // Assert
        $response->assertSessionHasErrors('sampah_id');
    }

    /** @test */
    public function store_fails_when_berat_item_is_below_minimum(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();
        $jenisSampah   = JenisSampah::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => [$jenisSampah->id],
            'berat'       => [0],
        ]);

        // Assert
        $response->assertStatus(422);
    }

    /** @test */
    public function store_fails_when_required_fields_are_missing(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), []);

        // Assert
        $response->assertStatus(422);
    }

    /** @test */
    public function store_fails_when_sampah_id_item_does_not_exist(): void
    {
        // Arrange
        $admin    = $this->adminUser();
        $pengepul = Pengepul::factory()->create();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.penjualan.store'), [
            'pengepul_id' => $pengepul->id,
            'sampah_id'   => [99999],
            'berat'       => [1],
        ]);

        // Assert
        $response->assertStatus(422);
    }
}
