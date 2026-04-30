<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_letters', function (Blueprint $table) {
            $table->longText('header')->nullable()->after('title');
            $table->longText('table_html')->nullable()->after('content');
            $table->longText('footer')->nullable()->after('table_html');
        });
    }

    public function down(): void
    {
        Schema::table('offer_letters', function (Blueprint $table) {
            $table->dropColumn(['header', 'table_html', 'footer']);
        });
    }
};

