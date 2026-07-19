<?php

namespace Tests\Unit;

use App\Support\HashRef;
use PHPUnit\Framework\TestCase;

class HashRefTest extends TestCase
{
    public function test_html_wraps_value_with_ltr_bdi_and_hash(): void
    {
        $html = HashRef::html('ORD-20260617-MGXFZJ')->toHtml();

        $this->assertStringContainsString('dir="ltr"', $html);
        $this->assertStringContainsString('class="hash-ref"', $html);
        $this->assertStringContainsString('#ORD-20260617-MGXFZJ', $html);
    }

    public function test_plain_prefixes_lrm_before_hash(): void
    {
        $plain = HashRef::plain(42);

        $this->assertStringStartsWith("\u{200E}#", $plain);
        $this->assertStringEndsWith('42', $plain);
    }
}
