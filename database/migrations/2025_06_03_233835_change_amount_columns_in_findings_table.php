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
            $table->unsignedDecimal('amount', 14, 2)
                ->nullable(false)
                ->default(0)
                ->change();
            $table->unsignedDecimal('surcharge_amount', 14, 2)
                ->default(0)
                ->change();
            $table->unsignedDecimal('amount_resolved', 14, 2)
                ->default(0)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->unsignedBigInteger('amount')->change()->nullable();
            $table->unsignedBigInteger('surcharge_amount')->change()->nullable();
            $table->unsignedBigInteger('amount_resolved')->change()->nullable();
        });
    }
};
