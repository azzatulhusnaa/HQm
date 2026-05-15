<?php

namespace App\Http\Controllers;

use App\Models\CalculationTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemplateController extends Controller
{
    public const FORMULA_TYPES = [
        'paper_size' => 'Print / Paper Size',
        'laminate' => 'Laminate',
        'spot_uv' => 'Spot UV',
        'diecut_punch' => 'Diecut Punch',
        'block' => 'Block',
        'glue_one_side' => 'Glue One Side',
        'tali' => 'Tali',
        'block_stamping' => 'Block Stamping',
        'area_multiplier' => 'Custom Area Formula',
    ];

    public function index()
    {
        $templates = CalculationTemplate::query()
            ->latest()
            ->paginate(10);

        return view('templates.index', [
            'templates' => $templates,
            'formulaTypes' => self::FORMULA_TYPES,
        ]);
    }

    public function create()
    {
        return view('templates.form', [
            'template' => new CalculationTemplate(['formula_type' => 'area_multiplier', 'is_active' => true, 'multiplier' => 0.004]),
            'formulaTypes' => self::FORMULA_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        CalculationTemplate::create($this->validatedTemplate($request));

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(CalculationTemplate $template)
    {
        return view('templates.form', [
            'template' => $template,
            'formulaTypes' => self::FORMULA_TYPES,
        ]);
    }

    public function update(Request $request, CalculationTemplate $template)
    {
        $template->update($this->validatedTemplate($request));

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(CalculationTemplate $template)
    {
        $template->delete();

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    private function validatedTemplate(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'formula_type' => ['required', Rule::in(array_keys(self::FORMULA_TYPES))],
            'description' => ['nullable', 'string', 'max:2000'],
            'multiplier' => ['required', 'numeric', 'min:0'],
            'minimum_charge' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
