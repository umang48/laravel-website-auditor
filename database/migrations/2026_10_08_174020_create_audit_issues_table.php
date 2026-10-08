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
        Schema::create('audit_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->cascadeOnDelete();
            // Pointing 'page_id' explicitly to the 'audit_pages' table
            $table->foreignId('page_id')->constrained('audit_pages')->cascadeOnDelete(); 
            $table->string('type'); // e.g., seo, performance, accessibility
            $table->string('severity'); // e.g., error, warning, info
            $table->text('message');
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_issues');
    }
};
