<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->string('hero_kicker', 80)->nullable()->after('sale_price');
            $table->string('hero_title', 180)->nullable()->after('hero_kicker');
            $table->string('hero_subtitle', 255)->nullable()->after('hero_title');
            $table->string('hero_button_text', 60)->nullable()->after('hero_subtitle');
            $table->string('hero_image')->nullable()->after('hero_button_text');
        });
    }

    public function down(): void
    {
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->dropColumn(['hero_kicker','hero_title','hero_subtitle','hero_button_text','hero_image']);
        });
    }
};
