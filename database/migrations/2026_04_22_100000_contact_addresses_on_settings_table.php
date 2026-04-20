<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->json('contact_addresses')->nullable()->after('warehouse_address');
        });

        foreach (DB::table('settings')->get() as $row) {
            $locations = [];
            $addr = trim((string) ($row->address ?? ''));
            if ($addr !== '') {
                $locations[] = [
                    'title' => 'Офис',
                    'address' => $addr,
                    'phones' => [],
                ];
            }
            $wh = trim((string) ($row->warehouse_address ?? ''));
            if ($wh !== '') {
                $locations[] = [
                    'title' => 'Склад',
                    'address' => $wh,
                    'phones' => [],
                ];
            }

            DB::table('settings')->where('id', $row->id)->update([
                'contact_addresses' => json_encode($locations, JSON_UNESCAPED_UNICODE),
            ]);
        }

        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn(['address', 'warehouse_address']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->string('address')->nullable();
            $table->string('warehouse_address')->nullable()->after('address');
        });

        foreach (DB::table('settings')->get() as $row) {
            $decoded = json_decode((string) ($row->contact_addresses ?? ''), true);
            $first = is_array($decoded) ? ($decoded[0] ?? null) : null;
            $second = is_array($decoded) ? ($decoded[1] ?? null) : null;
            DB::table('settings')->where('id', $row->id)->update([
                'address' => is_array($first) ? trim((string) ($first['address'] ?? '')) : null,
                'warehouse_address' => is_array($second) ? trim((string) ($second['address'] ?? '')) : null,
            ]);
        }

        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn('contact_addresses');
        });
    }
};
