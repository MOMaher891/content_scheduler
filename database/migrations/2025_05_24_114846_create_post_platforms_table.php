<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::Create('post_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts', 'id')->onDelete('cascade');
            $table->foreignId('platform_id')->constrained('platforms', 'id')->onDelete('cascade');
            $table->tinyInteger('platform_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_platforms', function (Blueprint $table) {
            //
        });
    }
};
