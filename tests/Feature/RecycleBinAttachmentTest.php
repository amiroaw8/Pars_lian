<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Services\SiteFileDeleter;
use App\Support\RecycleBinRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecycleBinAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_deleted_attachment_appears_in_recycle_bin(): void
    {
        Storage::fake('local');

        $admin = $this->createUserWithRole('super_admin');

        $order = ServiceOrder::factory()->create();
        $path = 'attachments/test.pdf';
        Storage::disk('local')->put($path, 'content');

        $attachment = Attachment::create([
            'name' => 'test.pdf',
            'path' => $path,
            'mime_type' => 'application/pdf',
            'size' => 7,
            'attachable_type' => ServiceOrder::class,
            'attachable_id' => $order->id,
            'uploaded_by' => $admin->id,
        ]);

        $attachment->delete();

        $deleted = RecycleBinRegistry::deletedItems();

        $this->assertTrue($deleted['attachments']->contains('id', $attachment->id));
        Storage::disk('local')->assertExists($path);
    }

    public function test_attachment_soft_delete_from_file_browser_keeps_disk_file(): void
    {
        Storage::fake('local');

        $order = ServiceOrder::factory()->create();
        $path = 'attachments/keep-me.pdf';
        Storage::disk('local')->put($path, 'content');

        $attachment = Attachment::create([
            'name' => 'keep-me.pdf',
            'path' => $path,
            'mime_type' => 'application/pdf',
            'size' => 7,
            'attachable_type' => ServiceOrder::class,
            'attachable_id' => $order->id,
            'uploaded_by' => User::factory()->create()->id,
        ]);

        app(SiteFileDeleter::class)->deleteByKey('attachment-'.$attachment->id);

        $this->assertSoftDeleted('attachments', ['id' => $attachment->id]);
        Storage::disk('local')->assertExists($path);
    }

    public function test_attachment_force_delete_from_recycle_bin_removes_disk_file(): void
    {
        Storage::fake('local');

        $admin = $this->createUserWithRole('super_admin');

        $order = ServiceOrder::factory()->create();
        $path = 'attachments/remove-me.pdf';
        Storage::disk('local')->put($path, 'content');

        $attachment = Attachment::create([
            'name' => 'remove-me.pdf',
            'path' => $path,
            'mime_type' => 'application/pdf',
            'size' => 7,
            'attachable_type' => ServiceOrder::class,
            'attachable_id' => $order->id,
            'uploaded_by' => $admin->id,
        ]);

        $attachment->delete();

        $this->actingAs($admin)
            ->withSession(['two_factor_verified' => true])
            ->delete(route('admin.recycle-bin.force-delete', ['type' => 'attachments', 'id' => $attachment->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_soft_deleted_attachments_are_excluded_from_file_catalog(): void
    {
        $order = ServiceOrder::factory()->create();

        $active = Attachment::create([
            'name' => 'active.pdf',
            'path' => 'attachments/active.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1,
            'attachable_type' => ServiceOrder::class,
            'attachable_id' => $order->id,
            'uploaded_by' => User::factory()->create()->id,
        ]);

        $trashed = Attachment::create([
            'name' => 'trashed.pdf',
            'path' => 'attachments/trashed.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1,
            'attachable_type' => ServiceOrder::class,
            'attachable_id' => $order->id,
            'uploaded_by' => User::factory()->create()->id,
        ]);
        $trashed->delete();

        $keys = app(\App\Services\SiteFileCatalog::class)->all()->pluck('key');

        $this->assertTrue($keys->contains('attachment-'.$active->id));
        $this->assertFalse($keys->contains('attachment-'.$trashed->id));
    }
}
