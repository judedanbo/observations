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
        Schema::create('parliaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_id')->constrained();
            $table->string('pac_directive');
            $table->date('pac_directive_date')->default(now());
            $table->string('client_responsible_officer')->nullable();
            $table->string('gas_assigned_officer')->nullable();
            $table->date('completed_by_date')->nullable();
            $table->date('implementation_date')->nullable();
            $table->string('status', 20)->default('open');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parliaments');
    }
};
