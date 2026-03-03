<?php

// database/migrations/xxxx_xx_xx_add_status_solved_at_to_service_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'status')) {
                $table->string('status')->default('pending')->after('video_path'); 
                // pending | solved
            }
            if (!Schema::hasColumn('service_requests', 'solved_at')) {
                $table->timestamp('solved_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'solved_at')) $table->dropColumn('solved_at');
            if (Schema::hasColumn('service_requests', 'status')) $table->dropColumn('status');
        });
    }
};