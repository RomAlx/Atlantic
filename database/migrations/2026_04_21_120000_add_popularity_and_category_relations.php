<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->unsignedBigInteger('view_count')->default(0)->after('sort_order');
            $table->boolean('boost_popular')->default(false)->after('view_count');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->json('related_category_ids')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['view_count', 'boost_popular']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('related_category_ids');
        });
    }
};
