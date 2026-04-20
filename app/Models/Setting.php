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
        'feedback_email',
        'yandex_metrika_counter_id',
        'yandex_maps_api_key',
        'contact_addresses',
        'socials',
        'phones',
        'social_links',
        'home_about_paragraph_1',
        'home_about_paragraph_2',
        'home_client_blocks',
        'seo_robots_txt_path',
        'seo_sitemap_xml_path',
    ];

    protected $casts = [
        'socials' => 'array',
        'phones' => 'array',
        'contact_addresses' => 'array',
        'social_links' => 'array',
        'home_client_blocks' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Setting $setting): void {
            $setting->home_client_blocks = static::normalizeHomeClientBlocks($setting->home_client_blocks);
            $setting->contact_addresses = static::normalizeContactAddresses($setting->contact_addresses);
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
     * Площадки с адресом и телефонами только этой площадки (без «основного» адреса).
     *
     * @return list<array{title: string, address: string, phones: list<array{label: string, number: string}>}>
     */
    public static function normalizeContactAddresses(mixed $rows): array
    {
        if (! is_array($rows)) {
            return [];
        }

        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? '')) ?: 'Площадка';
            $address = trim((string) ($row['address'] ?? ''));
            $phonesRaw = $row['phones'] ?? [];
            $phones = [];
            if (is_array($phonesRaw)) {
                foreach ($phonesRaw as $p) {
                    if (! is_array($p)) {
                        continue;
                    }
                    $number = trim((string) ($p['number'] ?? ''));
                    if ($number === '') {
                        continue;
                    }
                    $phones[] = [
                        'label' => trim((string) ($p['label'] ?? '')) ?: 'Телефон',
                        'number' => $number,
                    ];
                }
            }
            if ($address === '') {
                continue;
            }
            $out[] = [
                'title' => $title,
                'address' => $address,
                'phones' => $phones,
            ];
        }

        return $out;
    }

    /**
     * Телефоны без привязки к адресу; ровно один может быть основным (шапка сайта).
     *
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
