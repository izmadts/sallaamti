<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_contents', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // ayah|hadith
            $table->text('arabic_text')->nullable();
            $table->text('translation');
            $table->string('reference');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_contents');
    }
};
