<?php

namespace Database\Seeders;

use App\Models\CalculationTemplate;
use App\Models\DimensionOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
            ['Print / Paper Size', 'paper_size', 'Base paper price plus next 1000-sheet blocks.', 80, null],
            ['Laminate', 'laminate', 'Paper width x height x quantity x 0.00035.', 0.00035, null],
            ['Spot UV', 'spot_uv', 'Paper width x height x quantity x 0.00066.', 0.00066, null],
            ['Diecut Punch', 'diecut_punch', 'RM60 for every 1000 punches.', 60, null],
            ['Block', 'block', 'Length in mm x 0.045.', 0.045, null],
            ['Glue One Side', 'glue_one_side', 'RM0.06 per piece, minimum RM150.', 0.06, 150],
            ['Tali', 'tali', 'Total quantity x 0.042.', 0.042, null],
            ['Block Stamping', 'block_stamping', 'Formula not confirmed. Configure multiplier as needed.', 0, null],
        ] as [$name, $formulaType, $description, $multiplier, $minimumCharge]) {
            CalculationTemplate::updateOrCreate([
                'name' => $name,
            ], [
                'formula_type' => $formulaType,
                'description' => $description,
                'multiplier' => $multiplier,
                'minimum_charge' => $minimumCharge,
                'is_active' => true,
            ]);
        }

        foreach ([50, 100, 150, 200] as $value) {
            DimensionOption::firstOrCreate([
                'type' => 'width',
                'value' => $value,
            ], [
                'label' => $value.' width',
                'is_active' => true,
            ]);
        }

        foreach ([50, 100, 150, 200] as $value) {
            DimensionOption::firstOrCreate([
                'type' => 'height',
                'value' => $value,
            ], [
                'label' => $value.' height',
                'is_active' => true,
            ]);
        }

        foreach ([
            ['12.5 x 18.5', 12.5, 18.5, 90],
            ['25 x 18.5', 25, 18.5, 150],
            ['25 x 12.3', 25, 12.3, 150],
            ['25 x 37, 1000 sheets', 25, 37, 250],
            ['31 x 21.5, 1000 sheets', 31, 21.5, 250],
            ['28 x 40, 1000 sheets', 28, 40, 250],
        ] as [$label, $width, $height, $basePrice]) {
            DimensionOption::updateOrCreate([
                'type' => 'paper_size',
                'label' => $label,
            ], [
                'value' => $width,
                'width' => $width,
                'height' => $height,
                'base_price' => $basePrice,
                'included_quantity' => 1000,
                'is_active' => true,
            ]);
        }
    }
}
