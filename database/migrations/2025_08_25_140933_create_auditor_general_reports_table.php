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
        Schema::create('auditor_general_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('report_type', ['annual', 'quarterly', 'special', 'performance', 'thematic']);
            $table->year('report_year');
            $table->date('publication_date')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['draft', 'under_review', 'approved', 'published'])->default('draft');
            $table->text('executive_summary')->nullable();
            $table->text('methodology')->nullable();
            $table->text('conclusion')->nullable();
            $table->text('recommendations_summary')->nullable();
            $table->decimal('total_amount_involved', 15, 2)->default(0);
            $table->decimal('total_recoveries', 15, 2)->default(0);
            $table->integer('total_findings_count')->default(0);
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['report_year', 'report_type']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditor_general_reports');
    }
};
