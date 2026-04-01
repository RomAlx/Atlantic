<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('phones')->nullable()->after('phone');
            $table->json('social_links')->nullable()->after('socials');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('tree_path', 2048)->default('')->after('parent_id');
        });

        if (Schema::hasColumn('settings', 'phone')) {
            foreach (DB::table('settings')->get() as $row) {
                if (empty($row->phone)) {
                    continue;
                }
                $phones = [
                    [
                        'label' => 'Общий',
                        'number' => $row->phone,
                        'is_main' => true,
                    ],
                ];
                DB::table('settings')->where('id', $row->id)->update([
                    'phones' => json_encode($phones, JSON_UNESCAPED_UNICODE),
                ]);
            }
        }

        $settings = DB::table('settings')->whereNotNull('socials')->get();
        foreach ($settings as $s) {
            $decoded = json_decode((string) $s->socials, true);
            if (! is_array($decoded) || $decoded === []) {
                continue;
            }
            $links = [];
            foreach ($decoded as $key => $url) {
                if (! is_string($url) || trim($url) === '') {
                    continue;
                }
                $network = is_string($key) ? strtolower(preg_replace('/[^a-z0-9_]/i', '', $key)) : 'other';
                if ($network === '') {
                    $network = 'other';
                }
                $links[] = ['network' => $network, 'url' => trim($url)];
            }
            if ($links !== []) {
                DB::table('settings')->where('id', $s->id)->update([
                    'social_links' => json_encode($links, JSON_UNESCAPED_UNICODE),
                ]);
            }
        }

        $categories = DB::table('categories')->orderBy('id')->get();
        foreach ($categories as $cat) {
            $path = $this->buildTreePath((int) $cat->id, (int) ($cat->parent_id ?? 0), $categories);
            DB::table('categories')->where('id', $cat->id)->update(['tree_path' => $path]);
        }
    }

    /**
     * @param  Collection<int, object>  $all
     */
    private function buildTreePath(int $id, int $parentId, $all): string
    {
        if ($parentId === 0) {
            return '';
        }
        $segments = [];
        $currentParent = $parentId;
        $guard = 0;
        while ($currentParent > 0 && $guard++ < 100) {
            $segments[] = (string) $currentParent;
            $parentRow = $all->firstWhere('id', $currentParent);
            if (! $parentRow) {
                break;
            }
            $currentParent = (int) ($parentRow->parent_id ?? 0);
        }

        return implode('/', array_reverse($segments));
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('tree_path');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['phones', 'social_links']);
        });
    }
};
