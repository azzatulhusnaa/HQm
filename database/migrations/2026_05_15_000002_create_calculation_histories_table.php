<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculation_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculation_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('template_name');
            $table->string('formula_type')->default('area_multiplier');
            $table->decimal('width', 12, 2);
            $table->decimal('height', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->decimal('multiplier', 12, 6);
            $table->decimal('total', 14, 2);
            $table->json('breakdown')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_histories');
    }
};
