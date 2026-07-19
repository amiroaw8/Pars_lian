<?php

namespace Tests\Unit;

use App\Support\UserAgentParser;
use Tests\TestCase;

class SupportHelpersTest extends TestCase
{

    public function test_user_agent_parser_detects_browsers(): void
    {
        $this->assertSame('Google Chrome', UserAgentParser::browserLabel(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36'
        ));
        $this->assertSame('درخواست داخلی / سیستم', UserAgentParser::browserLabel('Symfony HttpClient/6.0'));
    }
}
