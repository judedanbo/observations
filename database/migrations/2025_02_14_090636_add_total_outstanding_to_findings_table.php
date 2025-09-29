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
            $table->decimal('amount_due', 14, 2)->unsigned()
                ->virtualAs('amount + surcharge_amount - amount_resolved');
            // $table->decimal('amount_due', 14, 2)->unsigned()
            //     ->virtualAs('amount + surcharge_amount - amount_resolved - outstanding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropColumn('outstanding');
        });
    }
};
