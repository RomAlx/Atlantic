<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('home_about_paragraph_1')->nullable()->after('social_links');
            $table->text('home_about_paragraph_2')->nullable()->after('home_about_paragraph_1');
            $table->json('home_client_blocks')->nullable()->after('home_about_paragraph_2');
            $table->boolean('yandex_metrika_enabled')->default(false)->after('home_client_blocks');
            $table->string('yandex_metrika_counter_id', 32)->nullable()->after('yandex_metrika_enabled');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_about_paragraph_1',
                'home_about_paragraph_2',
                'home_client_blocks',
                'yandex_metrika_enabled',
                'yandex_metrika_counter_id',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });
    }
};
