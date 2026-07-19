<?php

namespace Tests\Unit\Support;

use App\Support\RoleLabels;
use Tests\TestCase;

class RoleLabelsTest extends TestCase
{
    public function test_returns_persian_labels_for_known_roles(): void
    {
        $this->assertSame('پذیرش', RoleLabels::label('receptionist'));
        $this->assertSame('انباردار', RoleLabels::label('warehouse'));
        $this->assertSame('حسابدار', RoleLabels::label('accountant'));
        $this->assertSame('سوپر ادمین', RoleLabels::label('super_admin'));
    }
}
