<?php

namespace Database\Factories;

use App\Models\SMSLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class SMSLogFactory extends Factory
{
    protected $model = SMSLog::class;

    public function definition()
    {
        return [
            'phone' => '09123456789',
            'message' => $this->faker->sentence,
            'status' => 'sent',
            'sms_id' => $this->faker->numerify('##########'),
            'api_key_set' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
