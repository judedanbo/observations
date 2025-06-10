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
            $table->string('type', 20)->nullable();
            $table->unsignedDecimal('amount', 14, 2)
                ->nullable()
                ->default(0);
            $table->unsignedDecimal('surcharge_amount', 14, 2)
                ->nullable()
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('amount');
            $table->dropColumn('surcharge_amount');
        });
    }
};
