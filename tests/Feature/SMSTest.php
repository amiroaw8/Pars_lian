<?php

namespace Tests\Feature;

use App\Models\SMSLog;
use App\Models\User;
use App\Jobs\SendSmsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

use PHPUnit\Framework\Attributes\Test;

class SMSTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\UserRoleSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    #[Test]
    public function user_can_send_sms()
    {
        Bus::fake();

        $response = $this->actingAs($this->user)->postJson('/automation/sms/send', [
            'phone' => '09123456789',
            'message' => 'Test message',
        ]);

        $response->assertJson(['success' => true]);
        Bus::assertDispatched(SendSmsJob::class);
    }

    #[Test]
    public function sms_requires_valid_phone_number()
    {
        $response = $this->actingAs($this->user)->postJson('/automation/sms/send', [
            'phone' => 'invalid-phone',
            'message' => 'Test message',
        ]);

        $response->assertJsonValidationErrors('phone');
    }

    #[Test]
    public function sms_requires_message()
    {
        $response = $this->actingAs($this->user)->postJson('/automation/sms/send', [
            'phone' => '09123456789',
            'message' => '',
        ]);

        $response->assertJsonValidationErrors('message');
    }

    #[Test]
    public function user_can_view_sms_logs()
    {
        SMSLog::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get('/automation/sms/logs');

        $response->assertJson(['success' => true]);
        $response->assertJsonCount(5, 'data.data');
    }

    #[Test]
    public function user_can_check_sms_status()
    {
        $smsLog = SMSLog::factory()->create(['sms_id' => '12345']);

        Http::fake([
            'api.sms.ir/*' => Http::response([
                'status' => 1,
                'data' => ['status' => 'delivered'],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->get('/automation/sms/status/12345');

        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function sms_status_handles_missing_sms_id()
    {
        $response = $this->actingAs($this->user)->get('/automation/sms/status/99999');

        $response->assertJson(['success' => false]);
    }

    #[Test]
    public function sms_job_sends_sms_via_service()
    {
        Http::fake([
            'api.sms.ir/*' => Http::response([
                'status' => 1,
                'data' => ['messageIds' => ['12345']],
            ], 200),
        ]);

        $job = new SendSmsJob('09123456789', 'Test message');
        $job->handle(app(\App\Services\SMSService::class));

        $this->assertDatabaseHas('sms_logs', [
            'phone' => '09123456789',
            'message' => 'Test message',
            'status' => 'sent',
        ]);
    }
}
