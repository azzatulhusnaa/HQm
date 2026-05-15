<?php

namespace App\Http\Controllers;

use App\Models\CalculationHistory;
use App\Models\CalculationTemplate;
use App\Models\DimensionOption;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function index()
    {
        $templates = CalculationTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $latestHistories = CalculationHistory::query()
            ->latest()
            ->limit(5)
            ->get();

        $paperSizeOptions = DimensionOption::query()
            ->where('type', 'paper_size')
            ->where('is_active', true)
            ->orderBy('width')
            ->orderBy('height')
            ->get();

        return view('calculator.index', [
            'templates' => $templates,
            'paperSizeOptions' => $paperSizeOptions,
            'latestHistories' => $latestHistories,
            'result' => session('result'),
        ]);
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'calculation_template_id' => ['required', 'exists:calculation_templates,id'],
            'use_custom_size' => ['nullable', 'boolean'],
            'width_custom' => ['nullable', 'numeric', 'min:0'],
            'height_custom' => ['nullable', 'numeric', 'min:0'],
            'paper_size_option_id' => ['nullable', 'exists:dimension_options,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'multiplier' => ['required', 'numeric', 'min:0'],
        ]);

        $template = CalculationTemplate::query()
            ->where('is_active', true)
            ->findOrFail($validated['calculation_template_id']);

        if ($template->formula_type !== 'block') {
            $request->validate([
                'amount' => ['required', 'numeric', 'min:0'],
            ]);
        }

        $amount = $template->formula_type === 'block' ? 1 : (float) ($validated['amount'] ?? 0);
        $multiplier = (float) $validated['multiplier'];
        $usesCustomSize = $request->boolean('use_custom_size');
        $paperSize = $usesCustomSize ? null : $this->paperSize($validated['paper_size_option_id'] ?? null);
        $width = $paperSize ? (float) $paperSize->width : $this->dimensionValue('width', $validated['width_custom'] ?? null, $template->formula_type);
        $height = $paperSize ? (float) $paperSize->height : $this->dimensionValue('height', $validated['height_custom'] ?? null, $template->formula_type);
        $calculation = $this->calculateTemplate($template, $width, $height, $amount, $multiplier, $paperSize);

        CalculationHistory::create([
            'calculation_template_id' => $template->id,
            'template_name' => $template->name,
            'formula_type' => $template->formula_type,
            'width' => $width,
            'height' => $height,
            'amount' => $amount,
            'multiplier' => $multiplier,
            'total' => $calculation['total'],
            'breakdown' => $calculation['breakdown'],
        ]);

        return redirect()
            ->route('calculator.index')
            ->withInput()
            ->with('result', [
                'template' => $template->name,
                'formula_type' => $template->formula_type,
                'paper_size' => $paperSize?->label,
                'width' => $width,
                'height' => $height,
                'amount' => $amount,
                'multiplier' => $multiplier,
                'total' => $calculation['total'],
                'breakdown' => $calculation['breakdown'],
            ]);
    }

    private function dimensionValue(string $type, mixed $customValue, string $formulaType): float
    {
        if (in_array($formulaType, ['diecut_punch', 'glue_one_side', 'tali'], true)) {
            return 0;
        }

        if ($type === 'height' && $formulaType === 'block') {
            return 0;
        }

        if ($customValue !== null && $customValue !== '') {
            return (float) $customValue;
        }

        abort(422, ucfirst($type).' is required.');
    }

    private function paperSize(?int $optionId): ?DimensionOption
    {
        if (! $optionId) {
            return null;
        }

        return DimensionOption::query()
            ->where('type', 'paper_size')
            ->where('is_active', true)
            ->findOrFail($optionId);
    }

    private function calculateTemplate(CalculationTemplate $template, float $width, float $height, float $amount, float $multiplier, ?DimensionOption $paperSize): array
    {
        return match ($template->formula_type) {
            'paper_size' => $paperSize
                ? $this->calculatePaperSize($paperSize, $amount, $multiplier)
                : $this->calculateCustomSize($width, $height, $amount, $multiplier),
            'laminate', 'spot_uv', 'area_multiplier', 'block_stamping' => [
                'total' => $width * $height * $amount * $multiplier,
                'breakdown' => [
                    ['label' => 'Formula', 'value' => 'width x height x quantity x multiplier'],
                    ['label' => 'Calculation', 'value' => $this->number($width).' x '.$this->number($height).' x '.$this->number($amount).' x '.$this->number($multiplier)],
                ],
            ],
            'diecut_punch' => [
                'total' => ceil($amount / 1000) * $multiplier,
                'breakdown' => [
                    ['label' => 'Formula', 'value' => 'ceil(quantity / 1000) x price per 1000 punches'],
                    ['label' => 'Punch blocks', 'value' => (string) ceil($amount / 1000)],
                    ['label' => 'Calculation', 'value' => ceil($amount / 1000).' x '.$this->number($multiplier)],
                ],
            ],
            'block' => [
                'total' => $width * $multiplier,
                'breakdown' => [
                    ['label' => 'Formula', 'value' => 'length in mm x multiplier'],
                    ['label' => 'Calculation', 'value' => $this->number($width).' x '.$this->number($multiplier)],
                ],
            ],
            'glue_one_side' => $this->calculateGlueOneSide($template, $amount, $multiplier),
            'tali' => [
                'total' => $amount * $multiplier,
                'breakdown' => [
                    ['label' => 'Formula', 'value' => 'total quantity x multiplier'],
                    ['label' => 'Calculation', 'value' => $this->number($amount).' x '.$this->number($multiplier)],
                ],
            ],
            default => abort(422, 'Unsupported template formula.'),
        };
    }

    private function calculatePaperSize(?DimensionOption $paperSize, float $amount, float $nextThousandPrice): array
    {
        if (! $paperSize) {
            abort(422, 'Paper size is required.');
        }

        $includedQuantity = $paperSize->included_quantity ?: 1000;
        $extraSheets = max(0, $amount - $includedQuantity);
        $extraBlocks = (int) ceil($extraSheets / 1000);
        $basePrice = (float) $paperSize->base_price;
        $total = $basePrice + ($extraBlocks * $nextThousandPrice);

        return [
            'total' => $total,
            'breakdown' => [
                ['label' => 'Paper size', 'value' => $paperSize->label],
                ['label' => 'Base price', 'value' => 'RM'.$this->money($basePrice).' for '.$this->number($includedQuantity).' sheets'],
                ['label' => 'Extra 1000-sheet blocks', 'value' => (string) $extraBlocks],
                ['label' => 'Calculation', 'value' => 'RM'.$this->money($basePrice).' + ('.$extraBlocks.' x RM'.$this->money($nextThousandPrice).')'],
            ],
        ];
    }

    private function calculateCustomSize(float $width, float $height, float $amount, float $multiplier): array
    {
        return [
            'total' => $width * $height * $amount * $multiplier,
            'breakdown' => [
                ['label' => 'Size type', 'value' => 'Custom size'],
                ['label' => 'Formula', 'value' => 'width x height x quantity x multiplier'],
                ['label' => 'Calculation', 'value' => $this->number($width).' x '.$this->number($height).' x '.$this->number($amount).' x '.$this->number($multiplier)],
            ],
        ];
    }

    private function calculateGlueOneSide(CalculationTemplate $template, float $amount, float $multiplier): array
    {
        $subtotal = $amount * $multiplier;
        $minimumCharge = (float) ($template->minimum_charge ?? 150);

        return [
            'total' => max($subtotal, $minimumCharge),
            'breakdown' => [
                ['label' => 'Formula', 'value' => 'quantity x price per piece, minimum RM'.$this->money($minimumCharge)],
                ['label' => 'Subtotal', 'value' => 'RM'.$this->money($subtotal)],
                ['label' => 'Minimum charge', 'value' => 'RM'.$this->money($minimumCharge)],
            ],
        ];
    }

    private function number(float|int $value): string
    {
        return rtrim(rtrim(number_format($value, 6), '0'), '.');
    }

    private function money(float|int $value): string
    {
        return number_format($value, 2);
    }
}
