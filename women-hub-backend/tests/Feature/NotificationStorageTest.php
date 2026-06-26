<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Notifications\NewNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifiable_models_store_notifications_in_standard_database_table(): void
    {
        $user = User::factory()->create([
            'name' => 'Mentor User',
            'email' => 'mentor@example.com',
            'role' => 'mentor',
        ]);

        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        $user->notify(new NewNotification('Test title', 'Test body'));
        $admin->notify(new NewNotification('Admin title', 'Admin body'));

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NewNotification::class,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => Admin::class,
            'notifiable_id' => $admin->id,
            'type' => NewNotification::class,
        ]);

        $stored = $user->fresh()->notifications()->first();
        $this->assertSame('Test title', $stored->data['title'] ?? null);
    }
}
