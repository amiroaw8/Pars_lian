<?php

namespace Tests\Unit\Support;

use App\Support\AdminBreadcrumb;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminBreadcrumbTest extends TestCase
{
    public function test_accounting_path_builds_clickable_parent_links(): void
    {
        $this->app->instance('request', Request::create('/automation/dashboard/accounting', 'GET'));

        $items = AdminBreadcrumb::items();

        $this->assertCount(2, $items);
        $this->assertSame('میز کار', $items[0]['label']);
        $this->assertSame('/automation/dashboard', $items[0]['url']);
        $this->assertFalse($items[0]['current']);
        $this->assertSame('مالی', $items[1]['label']);
        $this->assertNull($items[1]['url']);
        $this->assertTrue($items[1]['current']);
    }

    public function test_service_order_show_builds_index_link(): void
    {
        $this->app->instance('request', Request::create('/automation/service-orders/42', 'GET'));

        $items = AdminBreadcrumb::items();

        $this->assertSame('پذیرش‌ها', $items[0]['label']);
        $this->assertSame('/automation/service-orders', $items[0]['url']);
        $this->assertSame('#42', $items[1]['label']);
        $this->assertTrue($items[1]['current']);
    }
}
