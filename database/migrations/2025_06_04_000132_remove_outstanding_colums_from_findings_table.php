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
            $table->unsignedDecimal('outstanding',)
                ->change()
                ->virtualAs('amount + surcharge_amount')
            ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->unsignedDecimal('outstanding')
                ->change()
                ->virtualAs('amount + surcharge_amount - amount_resolved');
        });
    }
};
