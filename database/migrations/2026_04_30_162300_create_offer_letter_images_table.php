<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_letter_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_letter_id')->constrained('offer_letters')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('path'); // public/uploads/offer_letters/...
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_letter_images');
    }
};

