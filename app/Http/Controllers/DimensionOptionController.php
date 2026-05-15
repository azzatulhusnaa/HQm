<?php

namespace App\Http\Controllers;

use App\Models\DimensionOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DimensionOptionController extends Controller
{
    public function index(string $type)
    {
        $options = DimensionOption::query()
            ->where('type', $type)
            ->latest()
            ->paginate(10);

        return view('dimension-options.index', [
            'type' => $type,
            'title' => $this->title($type),
            'options' => $options,
        ]);
    }

    public function create(string $type)
    {
        return view('dimension-options.form', [
            'type' => $type,
            'title' => $this->title($type),
            'option' => new DimensionOption(['type' => $type, 'is_active' => true]),
        ]);
    }

    public function store(Request $request, string $type)
    {
        DimensionOption::create($this->validatedOption($request, $type));

        return redirect()
            ->route($type.'-options.index')
            ->with('success', $this->title($type).' option created successfully.');
    }

    public function edit(string $type, DimensionOption $option)
    {
        abort_unless($option->type === $type, 404);

        return view('dimension-options.form', [
            'type' => $type,
            'title' => $this->title($type),
            'option' => $option,
        ]);
    }

    public function update(Request $request, string $type, DimensionOption $option)
    {
        abort_unless($option->type === $type, 404);

        $option->update($this->validatedOption($request, $type));

        return redirect()
            ->route($type.'-options.index')
            ->with('success', $this->title($type).' option updated successfully.');
    }

    public function destroy(string $type, DimensionOption $option)
    {
        abort_unless($option->type === $type, 404);

        $option->delete();

        return redirect()
            ->route($type.'-options.index')
            ->with('success', $this->title($type).' option deleted successfully.');
    }

    private function validatedOption(Request $request, string $type): array
    {
        $validated = $request->validate([
            'type' => ['nullable', Rule::in(['width', 'height', 'paper_size'])],
            'label' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric', 'min:0'],
            'width' => [$type === 'paper_size' ? 'required' : 'nullable', 'numeric', 'min:0'],
            'height' => [$type === 'paper_size' ? 'required' : 'nullable', 'numeric', 'min:0'],
            'base_price' => [$type === 'paper_size' ? 'required' : 'nullable', 'numeric', 'min:0'],
            'included_quantity' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['type'] = $type;
        $validated['value'] = $type === 'paper_size' ? $validated['width'] : $validated['value'];
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function title(string $type): string
    {
        return match ($type) {
            'paper_size' => 'Paper Size',
            default => ucfirst($type),
        };
    }
}
