<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Waterhole\Database\Migration;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('content_ip')) {
            return;
        }

        Schema::create('content_ip', function (Blueprint $table) {
            $table->string('content_type', 20);
            $table->unsignedBigInteger('content_id');
            $table->string('ip_address', 45);

            $table->primary(['content_type', 'content_id']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_ip');
    }
};