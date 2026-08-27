<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = config('app.url', 'https://plian.ir');

        $staticPages = [
            [
                'url' => route('home'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'url' => route('shop.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'url' => route('catalog.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'url' => route('catalog.index', ['on_sale' => 1]),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'url' => route('shop.about'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'url' => route('shop.contact'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'url' => route('shop.faq'),
                'lastmod' => now()->startOfWeek()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ],
            [
                'url' => route('shop.terms'),
                'lastmod' => now()->startOfYear()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ],
            [
                'url' => route('shop.privacy'),
                'lastmod' => now()->startOfYear()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ],
        ];

        $categories = ProductCategory::query()
            ->where('is_active', true)
            ->get()
            ->map(function ($cat) {
                return [
                    'url' => route('catalog.category', $cat->slug),
                    'lastmod' => ($cat->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            });

        $products = Product::query()
            ->active()
            ->inStock()
            ->get()
            ->map(function ($prod) {
                return [
                    'url' => route('catalog.show', $prod->slug),
                    'lastmod' => ($prod->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => '0.8',
                ];
            });

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($staticPages as $page) {
            $content .= "  <url>\n";
            $content .= '    <loc>' . htmlspecialchars($page['url'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $content .= '    <lastmod>' . $page['lastmod'] . "</lastmod>\n";
            $content .= '    <changefreq>' . $page['changefreq'] . "</changefreq>\n";
            $content .= '    <priority>' . $page['priority'] . "</priority>\n";
            $content .= "  </url>\n";
        }

        foreach ($categories as $cat) {
            $content .= "  <url>\n";
            $content .= '    <loc>' . htmlspecialchars($cat['url'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $content .= '    <lastmod>' . $cat['lastmod'] . "</lastmod>\n";
            $content .= '    <changefreq>' . $cat['changefreq'] . "</changefreq>\n";
            $content .= '    <priority>' . $cat['priority'] . "</priority>\n";
            $content .= "  </url>\n";
        }

        foreach ($products as $prod) {
            $content .= "  <url>\n";
            $content .= '    <loc>' . htmlspecialchars($prod['url'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $content .= '    <lastmod>' . $prod['lastmod'] . "</lastmod>\n";
            $content .= '    <changefreq>' . $prod['changefreq'] . "</changefreq>\n";
            $content .= '    <priority>' . $prod['priority'] . "</priority>\n";
            $content .= "  </url>\n";
        }

        $content .= '</urlset>';

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
