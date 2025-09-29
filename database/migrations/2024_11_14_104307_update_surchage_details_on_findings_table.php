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
            $table->unsignedBigInteger('amount')->change()->nullable();
            $table->unsignedBigInteger('surcharge_amount')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->unsigned()->change()->nullable();
            $table->decimal('surcharge_amount', 15, 2)->unsigned()->change()->nullable();
        });
    }
};
