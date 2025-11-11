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
        Schema::create('auditor_general_report_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auditor_general_report_id')
                ->constrained('auditor_general_reports')
                ->onDelete('cascade')
                ->name('ag_report_findings_ag_report_id_foreign');
            $table->foreignId('finding_id')
                ->constrained('findings')
                ->onDelete('cascade')
                ->name('ag_report_findings_finding_id_foreign');
            $table->integer('report_section_order')->default(0);
            $table->string('section_category')->nullable();
            $table->text('report_context')->nullable();
            $table->boolean('highlighted_finding')->default(false);
            $table->timestamps();

            $table->unique(['auditor_general_report_id', 'finding_id'], 'ag_report_finding_unique');
            $table->index('section_category');
            $table->index('report_section_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditor_general_report_findings');
    }
};
