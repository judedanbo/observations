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
        Schema::table('offices', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->after('district_id');
            $table->foreignId('parent_office_id')->nullable()->after('type')->constrained('offices')->nullOnDelete();
            $table->string('description', 255)->nullable()->after('parent_office_id');
            $table->boolean('is_active')->default(true)->after('description');
            $table->boolean('is_default')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropForeign(['parent_office_id']);
            $table->dropColumn('parent_office_id');
            $table->dropColumn('description');
            $table->dropColumn('is_active');
            $table->dropColumn('is_default');
        });
    }
};
