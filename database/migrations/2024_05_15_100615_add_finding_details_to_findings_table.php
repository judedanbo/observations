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
        Schema::table('findings', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->unsignedDecimal('amount')->nullable();
            $table->unsignedDecimal('surcharge_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->drop('type');
            $table->drop('amount');
            $table->drop('surcharge_amount');
        });
    }
};
