<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\CreatesRoles;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use CreatesRoles;

    public function actingAs(Authenticatable $user, $guard = null)
    {
        return parent::actingAs($user, $guard)->withSession([
            'two_factor_verified' => true,
        ]);
    }
}
