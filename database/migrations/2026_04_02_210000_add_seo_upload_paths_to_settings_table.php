<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('seo_robots_txt_path')->nullable()->after('yandex_metrika_counter_id');
            $table->string('seo_sitemap_xml_path')->nullable()->after('seo_robots_txt_path');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['seo_robots_txt_path', 'seo_sitemap_xml_path']);
        });
    }
};
