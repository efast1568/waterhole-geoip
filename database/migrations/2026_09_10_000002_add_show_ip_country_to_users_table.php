<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Waterhole\Database\Migration;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('users', 'show_ip_country')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('show_ip_country')->default(true);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'show_ip_country')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('show_ip_country');
        });
    }
};