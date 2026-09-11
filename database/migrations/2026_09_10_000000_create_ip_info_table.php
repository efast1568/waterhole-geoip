<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Waterhole\Database\Migration;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ip_info')) {
            return;
        }

        Schema::create('ip_info', function (Blueprint $table) {
            $table->string('ip_address')->unique();
            $table->string('country_code', 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_info');
    }
};