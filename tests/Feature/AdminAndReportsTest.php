<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAndReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_user_can_submit_report(): void
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['twofish_key' => bin2hex(random_bytes(32))])
            ->post('/reports', [
                'title' => 'Visualizer Error',
                'description' => 'Visualizer is not rendering on Chrome mobile version 114.',
            ]);

        $response->assertRedirect('/reports');
        $this->assertDatabaseHas('reports', [
            'user_id' => $user->id,
            'title' => 'Visualizer Error',
            'description' => 'Visualizer is not rendering on Chrome mobile version 114.',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_reply_to_report(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'user',
        ]);

        $report = Report::create([
            'user_id' => $user->id,
            'title' => 'Bug Report',
            'description' => 'Something is broken.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/reports/{$report->id}/reply", [
            'status' => 'resolved',
            'admin_reply' => 'This issue has been fixed in the latest patch.',
        ]);

        $response->assertRedirect("/admin/reports/{$report->id}");
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'resolved',
            'admin_reply' => 'This issue has been fixed in the latest patch.',
        ]);
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New User by Admin',
            'email' => 'newuser@tulip.com',
            'password' => 'newpassword123',
            'role' => 'user',
        ]);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('users', [
            'name' => 'New User by Admin',
            'email' => 'newuser@tulip.com',
            'role' => 'user',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Trash User',
            'email' => 'trash@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@tulip.com',
            'password' => bcrypt('password'),
            'master_key_salt' => base64_encode(random_bytes(24)),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }
}
