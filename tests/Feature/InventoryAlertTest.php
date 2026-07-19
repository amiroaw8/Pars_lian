<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use App\Jobs\SendSmsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_alert_sms_is_dispatched()
    {
        Queue::fake();

        // Create the warehouse role
        Role::create(['name' => 'warehouse']);

        // Create a warehouse manager
        $user = User::factory()->create([
            'phone' => '09123456789',
        ]);
        $user->assignRole('warehouse');

        // Create an inventory item
        $inventory = Inventory::factory()->create([
            'quantity' => 10,
            'min_quantity' => 5,
            'name' => 'Test Alert Item',
        ]);

        // Update quantity to trigger alert
        $inventory->update(['quantity' => 4]);

        // Assert job was pushed
        Queue::assertPushed(SendSmsJob::class, function ($job) use ($user) {
            return $job->getPhone() === $user->phone
                && str_contains($job->getMessage(), 'Test Alert Item');
        });
    }
}
