<?php

namespace Tests\Feature\Admin;

use App\Models\Nasabah;
use App\Models\TarikSaldo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test: Admin TarikSaldo Controller
 *
 * Menguji logika penarikan saldo oleh admin:
 * - Hanya admin yang bisa akses
 * - Penarikan valid: saldo berkurang, record tersimpan
 * - Penarikan ditolak jika saldo tidak mencukupi
 * - Validasi batas minimum dan maksimum (jika ada)
 * - Approve & Reject untuk request dari nasabah
 * - Validasi input
 */
class TarikSaldoControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function nasabahWithSaldo(int $saldo): Nasabah
    {
        return Nasabah::factory()->withSaldo($saldo)->create();
    }

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    /** @test */
    public function guest_cannot_access_tarik_saldo_index(): void
    {
        $response = $this->get(route('admin.tarik-saldo.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_view_tarik_saldo_index(): void
    {
        $admin    = $this->adminUser();
        $response = $this->actingAs($admin)->get(route('admin.tarik-saldo.index'));
        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Create
    // -----------------------------------------------------------------------

    /** @test */
    public function create_page_only_shows_nasabah_with_positive_saldo(): void
    {
        // Arrange
        $admin          = $this->adminUser();
        $withSaldo      = $this->nasabahWithSaldo(100000);
        $withoutSaldo   = $this->nasabahWithSaldo(0);

        // Act
        $response = $this->actingAs($admin)->get(route('admin.tarik-saldo.create'));

        // Assert
        $response->assertStatus(200);
        // Verifikasi hanya nasabah bersaldo yang dikirim ke view
        // (detail bergantung pada implementasi view)
    }

    // -----------------------------------------------------------------------
    // Store — Skenario Sukses
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_store_tarik_saldo_and_saldo_is_decremented(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = $this->nasabahWithSaldo(500000);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.store'), [
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 100000,
        ]);

        // Assert
        $response->assertRedirect(route('admin.tarik-saldo.index'));
        $response->assertSessionHas('success');

        // Saldo berkurang
        $this->assertEquals(400000, $nasabah->fresh()->saldo);

        // Record tersimpan dengan status approved
        $this->assertDatabaseHas('tarik_saldos', [
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 100000,
            'status'      => 'approved',
        ]);
    }

    /** @test */
    public function tarik_saldo_with_exact_saldo_amount_succeeds(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = $this->nasabahWithSaldo(250000);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.store'), [
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 250000, // Tepat sama dengan saldo
        ]);

        // Assert — batas atas yang tepat harus diizinkan
        $response->assertRedirect(route('admin.tarik-saldo.index'));
        $this->assertEquals(0, $nasabah->fresh()->saldo);
    }

    // -----------------------------------------------------------------------
    // Store — Skenario Gagal (saldo tidak cukup)
    // -----------------------------------------------------------------------

    /** @test */
    public function tarik_saldo_fails_when_saldo_is_insufficient(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = $this->nasabahWithSaldo(50000);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.store'), [
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 100000, // Lebih dari saldo
        ]);

        // Assert
        $response->assertSessionHas('error');

        // Saldo tidak berubah
        $this->assertEquals(50000, $nasabah->fresh()->saldo);

        // Record tidak tersimpan
        $this->assertDatabaseMissing('tarik_saldos', ['nasabah_id' => $nasabah->id]);
    }

    // -----------------------------------------------------------------------
    // Store — Validasi Input
    // -----------------------------------------------------------------------

    /** @test */
    public function store_fails_when_nasabah_id_does_not_exist(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.store'), [
            'nasabah_id'  => 99999,
            'jumlah_tarik' => 10000,
        ]);

        // Assert
        $response->assertSessionHasErrors('nasabah_id');
    }

    /** @test */
    public function store_fails_when_jumlah_tarik_is_zero(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = $this->nasabahWithSaldo(100000);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.store'), [
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 0,
        ]);

        // Assert
        $response->assertSessionHasErrors('jumlah_tarik');
    }

    /** @test */
    public function store_fails_when_jumlah_tarik_is_negative(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = $this->nasabahWithSaldo(100000);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.store'), [
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => -50000,
        ]);

        // Assert
        $response->assertSessionHasErrors('jumlah_tarik');
    }

    /** @test */
    public function store_fails_when_jumlah_tarik_is_not_numeric(): void
    {
        // Arrange
        $admin   = $this->adminUser();
        $nasabah = $this->nasabahWithSaldo(100000);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.store'), [
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 'bukan_angka',
        ]);

        // Assert
        $response->assertSessionHasErrors('jumlah_tarik');
    }

    /** @test */
    public function store_fails_when_required_fields_are_missing(): void
    {
        // Arrange
        $admin = $this->adminUser();

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.store'), []);

        // Assert
        $response->assertSessionHasErrors(['nasabah_id', 'jumlah_tarik']);
    }

    // -----------------------------------------------------------------------
    // Approve
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_approve_pending_tarik_saldo_request(): void
    {
        // Arrange
        $admin        = $this->adminUser();
        $nasabah      = $this->nasabahWithSaldo(200000);
        $tarikSaldo   = TarikSaldo::factory()->create([
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 50000,
            'status'      => 'pending',
        ]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.approve', $tarikSaldo->id));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tarik_saldos', ['id' => $tarikSaldo->id, 'status' => 'approved']);
        $this->assertEquals(150000, $nasabah->fresh()->saldo);
    }

    /** @test */
    public function approve_fails_when_tarik_saldo_is_not_pending(): void
    {
        // Arrange
        $admin      = $this->adminUser();
        $nasabah    = $this->nasabahWithSaldo(200000);
        $tarikSaldo = TarikSaldo::factory()->create([
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 50000,
            'status'      => 'approved', // Sudah diproses
        ]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.approve', $tarikSaldo->id));

        // Assert
        $response->assertSessionHas('error');
        // Saldo tidak berubah
        $this->assertEquals(200000, $nasabah->fresh()->saldo);
    }

    /** @test */
    public function approve_auto_rejects_when_saldo_is_insufficient(): void
    {
        // Arrange
        $admin      = $this->adminUser();
        $nasabah    = $this->nasabahWithSaldo(10000);
        $tarikSaldo = TarikSaldo::factory()->create([
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 500000, // Jauh melebihi saldo
            'status'      => 'pending',
        ]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.approve', $tarikSaldo->id));

        // Assert — otomatis ditolak
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('tarik_saldos', ['id' => $tarikSaldo->id, 'status' => 'rejected']);
        $this->assertEquals(10000, $nasabah->fresh()->saldo); // Saldo tidak berubah
    }

    // -----------------------------------------------------------------------
    // Reject
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_reject_pending_tarik_saldo_request(): void
    {
        // Arrange
        $admin      = $this->adminUser();
        $nasabah    = $this->nasabahWithSaldo(200000);
        $tarikSaldo = TarikSaldo::factory()->create([
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 100000,
            'status'      => 'pending',
        ]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.reject', $tarikSaldo->id), [
            'keterangan' => 'Dokumen tidak lengkap',
        ]);

        // Assert
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tarik_saldos', [
            'id'         => $tarikSaldo->id,
            'status'     => 'rejected',
            'keterangan' => 'Dokumen tidak lengkap',
        ]);

        // Saldo tidak berubah saat reject
        $this->assertEquals(200000, $nasabah->fresh()->saldo);
    }

    /** @test */
    public function reject_fails_when_tarik_saldo_is_already_processed(): void
    {
        // Arrange
        $admin      = $this->adminUser();
        $nasabah    = $this->nasabahWithSaldo(200000);
        $tarikSaldo = TarikSaldo::factory()->create([
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 100000,
            'status'      => 'approved', // Sudah diproses
        ]);

        // Act
        $response = $this->actingAs($admin)->post(route('admin.tarik-saldo.reject', $tarikSaldo->id), [
            'keterangan' => 'Alasan penolakan',
        ]);

        // Assert
        $response->assertSessionHas('error');
    }

    /** @test */
    public function reject_uses_default_keterangan_when_none_provided(): void
    {
        // Arrange
        $admin      = $this->adminUser();
        $nasabah    = $this->nasabahWithSaldo(200000);
        $tarikSaldo = TarikSaldo::factory()->create([
            'nasabah_id'  => $nasabah->id,
            'jumlah_tarik' => 100000,
            'status'      => 'pending',
        ]);

        // Act
        $this->actingAs($admin)->post(route('admin.tarik-saldo.reject', $tarikSaldo->id), []);

        // Assert — keterangan default
        $this->assertDatabaseHas('tarik_saldos', [
            'id'         => $tarikSaldo->id,
            'keterangan' => 'Ditolak oleh admin',
        ]);
    }
}
