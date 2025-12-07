<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_admissions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['national', 'mandiri']);
            $table->enum('category', ['snbp', 'snbt', 'mandiri'])->nullable();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();

            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->unsignedInteger('batch')->nullable();

            $table->string('status')->default('pending');
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_admissions');
    }
};
