<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_requests', function (Blueprint $table) {
            $table->foreignId('manager_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('in_progress_at')->nullable()->after('manager_id');
            $table->timestamp('completed_at')->nullable()->after('in_progress_at');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_requests', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['manager_id', 'in_progress_at', 'completed_at']);
        });
    }
};
