<?php

namespace App\Support;

use App\Models\Order;
use App\Models\ServiceOrder;

class InventoryTransactionNoteFormatter
{
    public static function toHtml(string $note, ?string $inventoryUrl = null): string
    {
        $raw = trim($note);

        if ($raw === '') {
            return '';
        }

        $replacements = [];

        foreach (self::repairOrderPhrases($raw) as $phrase => $serviceOrderId) {
            $replacements[$phrase] = self::anchor(
                route('automation.repairs.show', $serviceOrderId),
                $phrase,
                'text-amber-600'
            );
        }

        foreach (self::shopOrderPhrases($raw) as $phrase => $order) {
            $replacements[$phrase] = self::anchor(
                route('automation.orders.show', $order),
                $phrase,
                'text-primary-600'
            );
        }

        if ($inventoryUrl !== null && $inventoryUrl !== '') {
            $productLabel = self::productLabel($raw);
            if ($productLabel !== null) {
                $replacements[$productLabel] = self::anchor(
                    $inventoryUrl,
                    $productLabel,
                    'text-indigo-600'
                );
            }
        }

        uksort($replacements, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        $html = e($raw);
        foreach ($replacements as $search => $anchor) {
            $html = str_replace(e($search), $anchor, $html);
        }

        return $html;
    }

    /**
     * @return array<string, int>
     */
    private static function repairOrderPhrases(string $raw): array
    {
        $phrases = [];

        if (preg_match_all('/سفارش تعمیر #(\d+)/u', $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $id = (int) $match[1];
                if (ServiceOrder::query()->whereKey($id)->exists()) {
                    $phrases[$match[0]] = $id;
                }
            }
        }

        if (preg_match_all('/برگشت از تعمیر[^#]*#(\d+)/u', $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $id = (int) $match[1];
                if (! ServiceOrder::query()->whereKey($id)->exists()) {
                    continue;
                }

                $phrases['سفارش #'.$id] = $id;
                $phrases['سفارش تعمیر #'.$id] = $id;
            }
        }

        if (! str_contains($raw, 'سفارش تعمیر') && preg_match_all('/(?:^|[^\p{L}])سفارش #(\d+)/u', $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $id = (int) $match[1];
                $phrase = 'سفارش #'.$id;

                if (isset($phrases[$phrase]) || isset($phrases['سفارش تعمیر #'.$id])) {
                    continue;
                }

                if (ServiceOrder::query()->whereKey($id)->exists()) {
                    $phrases[$phrase] = $id;
                }
            }
        }

        return $phrases;
    }

    /**
     * @return array<string, Order>
     */
    private static function shopOrderPhrases(string $raw): array
    {
        $phrases = [];

        if (preg_match_all('/سفارش\s+(ORD-[\w-]+)/u', $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $orderNumber = $match[1];
                $order = Order::query()->where('order_number', $orderNumber)->first();

                if ($order) {
                    $phrases[$match[0]] = $order;
                }
            }
        }

        if (! str_contains($raw, 'سفارش تعمیر') && preg_match_all('/(?:^|[^\p{L}])سفارش #(\d+)/u', $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $id = (int) $match[1];
                $phrase = 'سفارش #'.$id;

                if (isset($phrases[$phrase])) {
                    continue;
                }

                if (ServiceOrder::query()->whereKey($id)->exists()) {
                    continue;
                }

                $order = Order::query()->find($id);
                if ($order) {
                    $phrases[$phrase] = $order;
                }
            }
        }

        return $phrases;
    }

    private static function productLabel(string $raw): ?string
    {
        if (! preg_match('/(?:لغو\/برگشت محصول|فروش حضوری محصول|فروش آنلاین محصول):\s*(.+?)(?:\s*—|$)/u', $raw, $match)) {
            return null;
        }

        $label = trim($match[1]);

        return $label !== '' ? $label : null;
    }

    private static function anchor(string $url, string $label, string $class): string
    {
        return '<a href="'.e($url).'" class="'.e($class).' font-bold hover:underline">'.e($label).'</a>';
    }
}
