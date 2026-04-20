<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function app(): View
    {
        $setting = Setting::query()->first();
        $counterId = trim((string) ($setting?->yandex_metrika_counter_id ?? ''));
        $mapsApiKey = trim((string) ($setting?->yandex_maps_api_key ?? ''));

        return view('site', [
            'yandexMetrikaCounterId' => $counterId !== '' ? $counterId : null,
            'yandexMapsApiKey' => $mapsApiKey !== '' ? $mapsApiKey : null,
        ]);
    }

    public function robots(): Response
    {
        $setting = Setting::query()->first();
        $path = $setting?->seo_robots_txt_path;

        if (filled($path) && Storage::disk('public')->exists($path)) {
            return response(Storage::disk('public')->get($path), 200)
                ->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        $content = "User-agent: *\nAllow: /\nSitemap: ".URL::to('/sitemap.xml')."\n";

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        $setting = Setting::query()->first();
        $path = $setting?->seo_sitemap_xml_path;

        if (filled($path) && Storage::disk('public')->exists($path)) {
            return response(Storage::disk('public')->get($path), 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8');
        }

        $base = rtrim(url('/'), '/');
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc>{$base}/</loc></url>
  <url><loc>{$base}/about</loc></url>
  <url><loc>{$base}/catalog</loc></url>
  <url><loc>{$base}/contacts</loc></url>
</urlset>
XML;

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
