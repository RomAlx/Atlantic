<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'phone',
        'email',
        'address',
        'warehouse_address',
        'socials',
        'phones',
        'social_links',
        'home_about_paragraph_1',
        'home_about_paragraph_2',
        'home_client_blocks',
        'yandex_metrika_enabled',
        'yandex_metrika_counter_id',
        'seo_robots_txt_path',
        'seo_sitemap_xml_path',
    ];

    protected $casts = [
        'socials' => 'array',
        'phones' => 'array',
        'social_links' => 'array',
        'home_client_blocks' => 'array',
        'yandex_metrika_enabled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Setting $setting): void {
            $setting->home_client_blocks = static::normalizeHomeClientBlocks($setting->home_client_blocks);
            $setting->phones = static::normalizePhones($setting->phones);
            $setting->social_links = static::normalizeSocialLinks($setting->social_links);
            $main = collect($setting->phones)->firstWhere('is_main', true);
            $setting->phone = $main['number'] ?? null;
            $setting->socials = collect($setting->social_links ?? [])
                ->filter(fn (array $row): bool => filled($row['url'] ?? null))
                ->mapWithKeys(fn (array $row): array => [(string) ($row['network'] ?? 'other') => (string) $row['url']])
                ->all();
        });
    }

    /**
     * @return list<array{label: string, number: string, is_main: bool}>
     */
    public static function normalizePhones(mixed $phones): array
    {
        if (! is_array($phones)) {
            return [];
        }

        $out = [];
        foreach ($phones as $row) {
            if (! is_array($row)) {
                continue;
            }
            $number = trim((string) ($row['number'] ?? ''));
            if ($number === '') {
                continue;
            }
            $out[] = [
                'label' => trim((string) ($row['label'] ?? '')) ?: 'Телефон',
                'number' => $number,
                'is_main' => (bool) ($row['is_main'] ?? false),
            ];
        }

        $mainIndex = null;
        foreach ($out as $i => $p) {
            if ($p['is_main']) {
                $mainIndex = $i;
                break;
            }
        }
        if ($mainIndex === null && $out !== []) {
            $out[0]['is_main'] = true;
            $mainIndex = 0;
        }
        foreach ($out as $i => &$p) {
            $p['is_main'] = ($i === $mainIndex);
        }
        unset($p);

        return $out;
    }

    /**
     * @param  mixed  $links
     * @return list<array{network: string, url: string}>
     */
    /**
     * @return list<array{title: string, text: string}>
     */
    public static function normalizeHomeClientBlocks(mixed $blocks): array
    {
        if (! is_array($blocks)) {
            return [];
        }

        $out = [];
        foreach ($blocks as $row) {
            if (! is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $text = trim((string) ($row['text'] ?? ''));
            if ($title === '' && $text === '') {
                continue;
            }
            $out[] = ['title' => $title !== '' ? $title : 'Блок', 'text' => $text];
        }

        return $out;
    }

    public static function normalizeSocialLinks(mixed $links): array
    {
        if (! is_array($links)) {
            return [];
        }

        $allowed = array_keys(config('site.social_networks', []));
        $out = [];
        foreach ($links as $row) {
            if (! is_array($row)) {
                continue;
            }
            $url = trim((string) ($row['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $network = strtolower((string) ($row['network'] ?? 'other'));
            if (! in_array($network, $allowed, true)) {
                $network = 'other';
            }
            $out[] = ['network' => $network, 'url' => $url];
        }

        return $out;
    }

    public function mainPhoneNumber(): ?string
    {
        $phones = $this->phones ?? [];
        $col = collect($phones);
        $main = $col->firstWhere('is_main', true);
        if (is_array($main) && filled($main['number'] ?? null)) {
            return (string) $main['number'];
        }
        $first = $col->first();
        if (is_array($first) && filled($first['number'] ?? null)) {
            return (string) $first['number'];
        }

        return $this->phone;
    }
}
