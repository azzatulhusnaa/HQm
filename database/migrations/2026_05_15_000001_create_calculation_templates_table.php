<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculation_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('formula_type')->default('area_multiplier');
            $table->text('description')->nullable();
            $table->decimal('multiplier', 12, 6)->default(0.004);
            $table->decimal('minimum_charge', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_templates');
    }
};
