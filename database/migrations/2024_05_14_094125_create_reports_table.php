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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained();
            $table->foreignId('audit_id')->constrained();
            $table->foreignId('finding_id')->constrained();
            $table->string('section')->nullable();
            $table->string('paragraphs', 20);
            $table->string('title');
            $table->string('type', 20)->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->longText('recommendation')->nullable();
            $table->unsignedInteger('amount_recovered')->nullable();
            $table->unsignedInteger('surcharge_amount')->nullable();
            $table->string('implementation_date', 10)->nullable();
            $table->string('implementation_status')->nullable();
            $table->longText('comments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
