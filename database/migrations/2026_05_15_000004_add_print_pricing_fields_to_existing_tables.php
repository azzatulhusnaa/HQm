<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calculation_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('calculation_templates', 'formula_type')) {
                $table->string('formula_type')->default('area_multiplier')->after('name');
            }

            if (! Schema::hasColumn('calculation_templates', 'minimum_charge')) {
                $table->decimal('minimum_charge', 12, 2)->nullable()->after('multiplier');
            }
        });

        Schema::table('calculation_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('calculation_histories', 'formula_type')) {
                $table->string('formula_type')->default('area_multiplier')->after('template_name');
            }

            if (! Schema::hasColumn('calculation_histories', 'breakdown')) {
                $table->json('breakdown')->nullable()->after('total');
            }
        });

        Schema::table('dimension_options', function (Blueprint $table) {
            if (! Schema::hasColumn('dimension_options', 'width')) {
                $table->decimal('width', 12, 2)->nullable()->after('value');
            }

            if (! Schema::hasColumn('dimension_options', 'height')) {
                $table->decimal('height', 12, 2)->nullable()->after('width');
            }

            if (! Schema::hasColumn('dimension_options', 'base_price')) {
                $table->decimal('base_price', 12, 2)->nullable()->after('height');
            }

            if (! Schema::hasColumn('dimension_options', 'included_quantity')) {
                $table->unsignedInteger('included_quantity')->nullable()->after('base_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('calculation_templates', function (Blueprint $table) {
            $table->dropColumn(['formula_type', 'minimum_charge']);
        });

        Schema::table('calculation_histories', function (Blueprint $table) {
            $table->dropColumn(['formula_type', 'breakdown']);
        });

        Schema::table('dimension_options', function (Blueprint $table) {
            $table->dropColumn(['width', 'height', 'base_price', 'included_quantity']);
        });
    }
};
