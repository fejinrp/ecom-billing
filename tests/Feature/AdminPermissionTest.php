<?php

namespace Tests\Feature;

use App\Models\Auser;
use App\Models\Usercheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Super Admins (section = 1) bypass all permission checks.
     */
    public function test_super_admin_bypasses_all_permission_checks(): void
    {
        $superAdmin = Auser::create([
            'username' => 'superadmin',
            'password' => bcrypt('password'),
            'email' => 'super@example.com',
            'mobile' => '1234567890',
            'section' => 1, // Super Admin
            'ustatus' => 1,
        ]);

        // Access backup dashboard (normally restricted to backup permission = 1)
        $response = $this
            ->actingAs($superAdmin, 'admin')
            ->get(route('admin.backups.index'));

        // Should bypass and load successfully (or try to load, since route is registered and view exists)
        $response->assertStatus(200);
    }

    /**
     * Test that Executive Admins (section = 2) are redirected when permission is disabled (0).
     */
    public function test_executive_is_denied_when_permission_is_disabled(): void
    {
        $exec = Auser::create([
            'username' => 'executive_user',
            'password' => bcrypt('password'),
            'email' => 'exec@example.com',
            'mobile' => '0987654321',
            'section' => 2, // Executive
            'ustatus' => 1,
        ]);

        // Setup usercheck record with backup permission disabled (0)
        Usercheck::create([
            'uid' => $exec->user_id,
            'backup' => 0,
            'cat' => 1,
        ]);

        $response = $this
            ->actingAs($exec, 'admin')
            ->get(route('admin.backups.index'));

        // Should intercept, redirect to dashboard with error alert
        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error', 'Access Denied: You do not have permission to access that module.');
    }

    /**
     * Test that Executive Admins (section = 2) can access when permission is enabled (1).
     */
    public function test_executive_is_allowed_when_permission_is_enabled(): void
    {
        $exec = Auser::create([
            'username' => 'executive_user2',
            'password' => bcrypt('password'),
            'email' => 'exec2@example.com',
            'mobile' => '0987654322',
            'section' => 2, // Executive
            'ustatus' => 1,
        ]);

        // Setup usercheck record with backup permission enabled (1)
        Usercheck::create([
            'uid' => $exec->user_id,
            'backup' => 1,
            'cat' => 1,
        ]);

        $response = $this
            ->actingAs($exec, 'admin')
            ->get(route('admin.backups.index'));

        // Should allow access
        $response->assertStatus(200);
    }

    /**
     * Test that exempted routes like Dashboard and settings are accessible.
     */
    public function test_exempt_routes_are_always_accessible(): void
    {
        $exec = Auser::create([
            'username' => 'executive_user3',
            'password' => bcrypt('password'),
            'email' => 'exec3@example.com',
            'mobile' => '0987654323',
            'section' => 2, // Executive
            'ustatus' => 1,
        ]);

        // Setup usercheck record with everything disabled
        Usercheck::create([
            'uid' => $exec->user_id,
            'backup' => 0,
            'usett' => 0,
        ]);

        // Try accessing Dashboard
        $response = $this
            ->actingAs($exec, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Try accessing Setting Admin (own settings profile)
        $response = $this
            ->actingAs($exec, 'admin')
            ->get(route('admin.settings.index'));

        $response->assertStatus(200);
    }
}
