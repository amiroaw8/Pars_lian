<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleanupTwoFactorCodesTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_clears_expired_two_factor_codes(): void
    {
        $user = User::factory()->create([
            'two_factor_code' => '123456',
            'two_factor_expires_at' => now()->subMinute(),
        ]);

        $this->artisan('auth:cleanup-two-factor-codes')
            ->assertSuccessful();

        $user->refresh();

        $this->assertNull($user->two_factor_code);
        $this->assertNull($user->two_factor_expires_at);
    }
}
