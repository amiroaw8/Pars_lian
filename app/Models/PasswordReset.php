<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_resets';
    public $timestamps = false;

    protected $fillable = ['phone', 'code', 'expires_at', 'verified_at', 'reset_token'];
    protected $casts = ['expires_at' => 'datetime', 'verified_at' => 'datetime'];
}
