<?php

namespace Tests\Unit;

use App\Support\ShopFormat;
use PHPUnit\Framework\TestCase;

class ShopFormatTest extends TestCase
{
    public function test_decimal_string_amounts_parse_without_scaling(): void
    {
        $this->assertSame(350_000_000, ShopFormat::toIntegerAmount('350000000.00'));
        $this->assertSame(700_300_000, ShopFormat::toIntegerAmount('700300000.00'));
        $this->assertSame('350,000,000', ShopFormat::moneyAmount('350000000.00'));
    }

    public function test_formatted_and_integer_amounts_match_words(): void
    {
        $amount = 350_000_000;

        $this->assertSame(
            ShopFormat::amountInWords($amount),
            ShopFormat::amountInWords('350000000.00')
        );
        $this->assertSame(
            ShopFormat::amountInWords($amount),
            ShopFormat::amountInWords('350,000,000')
        );
    }

    public function test_amount_in_words_for_common_repair_totals(): void
    {
        $this->assertSame('سیصد و پنجاه میلیون تومان', ShopFormat::amountInWords(350_000_000));
        $this->assertSame('هفتصد میلیون و سیصد هزار تومان', ShopFormat::amountInWords(700_300_000));
    }
}
