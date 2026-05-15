@extends('layouts.app', ['title' => 'Calculator'])

@section('content')
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-yellow-300">Calculator</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">Calculate a clean total</h1>
            <p class="mt-2 max-w-2xl text-zinc-400">Choose a template, pick a paper size when needed, then enter quantity and rate.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('paper_size-options.index') }}" class="btn-secondary">Manage Paper Sizes</a>
            <a href="{{ route('templates.create') }}" class="btn-primary">Add Template</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="card p-6">
            <form method="POST" action="{{ route('calculator.calculate') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="label" for="calculation_template_id">Template</label>
                    <select id="calculation_template_id" name="calculation_template_id" class="input">
                        <option value="">Choose a template</option>
                        @foreach ($templates as $template)
                            <option
                                value="{{ $template->id }}"
                                data-formula-type="{{ $template->formula_type }}"
                                data-multiplier="{{ $template->multiplier }}"
                                @selected(old('calculation_template_id') == $template->id)
                            >
                                {{ $template->name }} (x {{ rtrim(rtrim(number_format($template->multiplier, 6), '0'), '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('calculation_template_id') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div data-field="paper_size" class="space-y-4">
                    <div data-field="paper_size_select">
                        <label class="label" for="paper_size_option_id">Paper Size</label>
                        <select id="paper_size_option_id" name="paper_size_option_id" class="input">
                            <option value="">Select paper size</option>
                            @foreach ($paperSizeOptions as $option)
                                <option value="{{ $option->id }}" @selected(old('paper_size_option_id') == $option->id)>
                                    {{ rtrim(rtrim(number_format($option->width, 2), '0'), '.') }} x {{ rtrim(rtrim(number_format($option->height, 2), '0'), '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('paper_size_option_id') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <label class="inline-flex items-center gap-3 rounded-lg border border-zinc-800 bg-zinc-900 px-4 py-3 text-sm font-black text-zinc-100">
                        <input id="use_custom_size" name="use_custom_size" value="1" type="checkbox" class="h-5 w-5 rounded border-zinc-300 text-yellow-400 focus:ring-yellow-400" @checked(old('use_custom_size'))>
                        Use Custom Size
                    </label>

                    <div data-field="custom_size" class="grid gap-4 rounded-lg border border-zinc-800 bg-zinc-900 p-4 sm:grid-cols-2">
                        <div>
                            <label class="label" for="width_custom" data-width-label>Width</label>
                            <input id="width_custom" name="width_custom" type="number" step="0.01" min="0" value="{{ old('width_custom') }}" class="input" placeholder="12.5">
                            @error('width_custom') <p class="error">{{ $message }}</p> @enderror
                        </div>

                        <div data-field="height">
                            <label class="label" for="height_custom">Height</label>
                            <input id="height_custom" name="height_custom" type="number" step="0.01" min="0" value="{{ old('height_custom') }}" class="input" placeholder="18.5">
                            @error('height_custom') <p class="error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2" data-field="pricing_inputs">
                    <div data-field="amount">
                        <label class="label" for="amount" data-amount-label>Quantity / Sheets</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="0" value="{{ old('amount') }}" class="input" placeholder="3">
                        @error('amount') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label" for="multiplier" data-multiplier-label>Multiplier</label>
                        <input id="multiplier" name="multiplier" type="number" step="0.000001" min="0" value="{{ old('multiplier') }}" class="input" placeholder="0.00035">
                        @error('multiplier') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="border-t border-zinc-800 pt-2">
                    <button type="submit" class="btn-primary w-full sm:w-auto">Calculate Total</button>
                </div>
            </form>
        </section>

        <aside class="card overflow-hidden">
            <div class="border-b border-zinc-800 bg-zinc-900 px-6 py-5 text-white">
                <p class="text-sm font-bold text-yellow-300">Result</p>
                <h2 class="mt-1 text-2xl font-black">Calculation breakdown</h2>
            </div>

            <div class="p-6">
                @if ($result)
                    <div class="rounded-lg bg-yellow-400 p-5 text-black">
                        <p class="text-sm font-bold uppercase tracking-[0.14em]">Total</p>
                        <p class="mt-2 text-4xl font-black">{{ number_format($result['total'], 2) }}</p>
                    </div>

                    <div class="mt-5 space-y-3 text-sm">
                        <div class="breakdown-row"><span>Template</span><strong>{{ $result['template'] }}</strong></div>
                        @if ($result['paper_size'])
                            <div class="breakdown-row"><span>Paper size</span><strong>{{ $result['paper_size'] }}</strong></div>
                        @endif
                        <div class="breakdown-row"><span>{{ $result['formula_type'] === 'block' ? 'Length' : 'Width' }}</span><strong>{{ number_format($result['width'], 2) }}</strong></div>
                        @if ($result['formula_type'] !== 'block')
                            <div class="breakdown-row"><span>Height</span><strong>{{ number_format($result['height'], 2) }}</strong></div>
                            <div class="breakdown-row"><span>Quantity</span><strong>{{ number_format($result['amount'], 2) }}</strong></div>
                        @endif
                        <div class="breakdown-row"><span>Multiplier</span><strong>{{ rtrim(rtrim(number_format($result['multiplier'], 6), '0'), '.') }}</strong></div>
                        @foreach ($result['breakdown'] as $line)
                            <div class="breakdown-row"><span>{{ $line['label'] }}</span><strong>{{ $line['value'] }}</strong></div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-zinc-700 p-6 text-center">
                        <p class="text-lg font-black text-zinc-100">No calculation yet</p>
                        <p class="mt-2 text-sm text-zinc-400">Your total and formula breakdown will appear here.</p>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    <section class="mt-8 card p-6">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-xl font-black">Recent history</h2>
            <a href="{{ route('history.index') }}" class="text-sm font-bold text-yellow-300 underline decoration-yellow-400 decoration-2 underline-offset-4">View all</a>
        </div>

        @if ($latestHistories->isEmpty())
            <p class="text-sm text-zinc-400">No saved calculations yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Template</th>
                        <th>Formula</th>
                        <th class="text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($latestHistories as $history)
                        <tr>
                            <td class="font-bold">{{ $history->template_name }}</td>
                            <td>{{ $history->breakdown[0]['value'] ?? $history->formula_type }}</td>
                            <td class="text-right font-black">{{ number_format($history->total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <script>
        const templateSelect = document.getElementById('calculation_template_id');
        const multiplierInput = document.getElementById('multiplier');
        const paperSizeField = document.querySelector('[data-field="paper_size"]');
        const paperSizeSelectField = document.querySelector('[data-field="paper_size_select"]');
        const customSizeToggle = document.getElementById('use_custom_size');
        const customSizeField = document.querySelector('[data-field="custom_size"]');
        const paperSizeSelect = document.getElementById('paper_size_option_id');
        const amountField = document.querySelector('[data-field="amount"]');
        const pricingInputs = document.querySelector('[data-field="pricing_inputs"]');
        const heightField = document.querySelector('[data-field="height"]');
        const widthLabel = document.querySelector('[data-width-label]');
        const amountLabel = document.querySelector('[data-amount-label]');
        const multiplierLabel = document.querySelector('[data-multiplier-label]');
        const sizeBasedTypes = ['paper_size', 'laminate', 'spot_uv', 'area_multiplier', 'block_stamping', 'block'];

        function syncTemplateFields() {
            const selected = templateSelect.options[templateSelect.selectedIndex];
            const type = selected.dataset.formulaType || '';
            const usesSize = sizeBasedTypes.includes(type);

            if (!multiplierInput.value && selected.dataset.multiplier) {
                multiplierInput.value = selected.dataset.multiplier;
            }

            paperSizeField.hidden = !usesSize;
            paperSizeSelectField.hidden = type === 'block';
            paperSizeSelect.disabled = !usesSize || customSizeToggle.checked || type === 'block';
            customSizeToggle.closest('label').hidden = !usesSize || type === 'block';
            customSizeField.hidden = !usesSize || (!customSizeToggle.checked && type !== 'block');
            heightField.hidden = type === 'block';
            amountField.hidden = type === 'block';
            pricingInputs.classList.toggle('sm:grid-cols-1', type === 'block');
            pricingInputs.classList.toggle('sm:grid-cols-2', type !== 'block');
            widthLabel.textContent = type === 'block' ? 'Length in mm' : 'Width';
            amountLabel.textContent = type === 'diecut_punch' ? 'Punch quantity' : (type === 'paper_size' ? 'Sheets' : 'Quantity / Total');
            multiplierLabel.textContent = {
                paper_size: 'Next 1000 sheets price',
                diecut_punch: 'Price per 1000 punches',
                glue_one_side: 'Price per piece',
                block: 'Multiplier per mm',
                tali: 'Multiplier',
                block_stamping: 'Configurable multiplier',
            }[type] || 'Multiplier';
        }

        templateSelect.addEventListener('change', () => {
            multiplierInput.value = '';
            syncTemplateFields();
        });

        customSizeToggle.addEventListener('change', syncTemplateFields);

        syncTemplateFields();
    </script>
@endsection
