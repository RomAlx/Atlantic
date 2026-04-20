<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table): void {
            $table->id();
            $table->string('path', 255);
            $table->string('route_name', 64)->nullable();
            $table->string('section', 64);
            $table->string('product_slug', 255)->nullable();
            $table->string('support_article_slug', 255)->nullable();
            $table->timestamp('visited_at');
            $table->timestamps();

            $table->index(['visited_at']);
            $table->index(['section', 'visited_at']);
            $table->index(['product_slug', 'visited_at']);
            $table->index(['support_article_slug', 'visited_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
