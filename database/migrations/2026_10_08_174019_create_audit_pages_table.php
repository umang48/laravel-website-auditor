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
        Schema::create('audit_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->integer('status_code')->nullable();
            $table->integer('response_time')->nullable(); // stored in milliseconds
            $table->integer('page_size')->nullable(); // stored in bytes
            $table->string('title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_pages');
    }
};
