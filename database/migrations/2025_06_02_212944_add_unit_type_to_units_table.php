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
        Schema::table('units', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->after('description');
            $table->string('short_name', 10)->nullable()->after('type');
            $table->boolean('is_active')->default(true)->after('short_name');
            $table->boolean('is_default')->default(false)->after('is_active');
            $table->foreignId('office_id')->nullable()->after('is_default')->constrained('offices')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('unit_code');
            $table->dropColumn('unit_short_name');
            $table->dropColumn('unit_description');
            $table->dropColumn('is_active');
            $table->dropColumn('is_default');
            $table->dropForeign(['office_id']);
            $table->dropColumn('office_id');
        });
    }
};
