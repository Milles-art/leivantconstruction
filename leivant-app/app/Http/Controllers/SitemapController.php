<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            '/',
            '/about',
            '/services',
            '/projects',
            '/products',
            '/discovery',
            '/solution',
            '/contact',
            '/privacy',
            '/terms',
        ]);

        $serviceSlugs = [
            'house-construction-tanzania',
            'boq-preparation-tanzania',
            'construction-equipment-rental',
            'renovation-contractor-dar-es-salaam',
            'building-materials-supply',
            'architecture',
            'construction',
            'engineering',
            'skilled-labour',
            'site-support',
        ];

        foreach ($serviceSlugs as $slug) {
            $urls->push('/services/'.$slug);
        }

        foreach (config('vant_products.products', []) as $product) {
            if (! empty($product['slug'])) {
                $urls->push('/products/'.$product['slug']);
            }
        }

        $lastmod = now()->toAtomString();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls->filter()->unique()->values() as $path) {
            $priority = match (true) {
                $path === '/' => '1.00',
                str_starts_with($path, '/services') => '0.85',
                str_starts_with($path, '/products') => '0.75',
                default => '0.80',
            };

            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e(url($path))."</loc>\n";
            $xml .= '    <lastmod>'.$lastmod."</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= '    <priority>'.$priority."</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}

