<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Feature Test: CheckRole Middleware
 *
 * Menguji middleware CheckRole:
 * - Guest diblokir (403)
 * - User dengan role yang salah diblokir (403)
 * - User dengan role yang benar diteruskan (pass through)
 */
class CheckRoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Definisikan route sementara untuk pengujian middleware
        Route::middleware(['auth', 'role:admin'])->get('/test-admin-route', function () {
            return response('OK', 200);
        });

        Route::middleware(['auth', 'role:nasabah'])->get('/test-nasabah-route', function () {
            return response('OK', 200);
        });
    }

    // -----------------------------------------------------------------------
    // Guest
    // -----------------------------------------------------------------------

    /** @test */
    public function guest_is_blocked_from_admin_route(): void
    {
        $response = $this->get('/test-admin-route');
        // Redirected to login (auth middleware) or forbidden
        $this->assertContains($response->status(), [302, 403]);
    }

    // -----------------------------------------------------------------------
    // Role mismatch
    // -----------------------------------------------------------------------

    /** @test */
    public function nasabah_user_is_blocked_from_admin_route(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'nasabah']);

        // Act
        $response = $this->actingAs($user)->get('/test-admin-route');

        // Assert
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_user_is_blocked_from_nasabah_route(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);

        // Act
        $response = $this->actingAs($user)->get('/test-nasabah-route');

        // Assert
        $response->assertStatus(403);
    }

    // -----------------------------------------------------------------------
    // Role match
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_user_can_access_admin_route(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);

        // Act
        $response = $this->actingAs($user)->get('/test-admin-route');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function nasabah_user_can_access_nasabah_route(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'nasabah']);

        // Act
        $response = $this->actingAs($user)->get('/test-nasabah-route');

        // Assert
        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // Unit test langsung pada class middleware
    // -----------------------------------------------------------------------

    /** @test */
    public function middleware_handle_passes_when_role_matches(): void
    {
        // Arrange
        $middleware = new CheckRole();
        $user       = User::factory()->make(['role' => 'admin']);

        $this->actingAs($user);
        $request = Request::create('/test', 'GET');

        $called = false;
        $next   = function ($req) use (&$called) {
            $called = true;

            return response('passed', 200);
        };

        // Act
        $response = $middleware->handle($request, $next, 'admin');

        // Assert
        $this->assertTrue($called);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function middleware_handle_aborts_403_when_role_does_not_match(): void
    {
        // Arrange
        $middleware = new CheckRole();
        $user       = User::factory()->make(['role' => 'nasabah']);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $next = fn ($req) => response('should not reach', 200);

        // Assert
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        // Act
        $middleware->handle($request, $next, 'admin');
    }

    /** @test */
    public function middleware_handle_aborts_403_when_user_is_not_authenticated(): void
    {
        // Arrange
        $middleware = new CheckRole();

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => null); // Guest

        $next = fn ($req) => response('should not reach', 200);

        // Assert
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        // Act
        $middleware->handle($request, $next, 'admin');
    }
}
