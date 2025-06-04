<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recoveries', function (Blueprint $table) {
            $table->unsignedDecimal('amount', 14, 2)->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recoveries', function (Blueprint $table) {
            $table->unsignedDouble('amount')->change();
        });
    }
};
