<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dimension_options', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('label');
            $table->decimal('value', 12, 2);
            $table->decimal('width', 12, 2)->nullable();
            $table->decimal('height', 12, 2)->nullable();
            $table->decimal('base_price', 12, 2)->nullable();
            $table->unsignedInteger('included_quantity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dimension_options');
    }
};
