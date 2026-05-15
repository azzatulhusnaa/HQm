@extends('layouts.app', ['title' => $template->exists ? 'Edit Template' : 'Add Template'])

@section('content')
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-yellow-300">{{ $template->exists ? 'Edit' : 'Add' }} template</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">{{ $template->exists ? 'Update template' : 'Create template' }}</h1>
    </div>

    <section class="card p-6">
        <form method="POST" action="{{ $template->exists ? route('templates.update', $template) : route('templates.store') }}" class="space-y-5">
            @csrf
            @if ($template->exists)
                @method('PUT')
            @endif

            <div>
                <label class="label" for="name">Template name</label>
                <input id="name" name="name" class="input" value="{{ old('name', $template->name) }}" placeholder="Standard Area Calculator">
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="formula_type">Calculation type</label>
                <select id="formula_type" name="formula_type" class="input">
                    @foreach ($formulaTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('formula_type', $template->formula_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('formula_type') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label" for="description">Description</label>
                <textarea id="description" name="description" class="input min-h-28" placeholder="Width x height x amount x multiplier">{{ old('description', $template->description) }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="multiplier">Default multiplier / rate</label>
                    <input id="multiplier" name="multiplier" type="number" step="0.000001" min="0" class="input" value="{{ old('multiplier', $template->multiplier) }}" placeholder="0.004">
                    @error('multiplier') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label" for="minimum_charge">Minimum charge</label>
                    <input id="minimum_charge" name="minimum_charge" type="number" step="0.01" min="0" class="input" value="{{ old('minimum_charge', $template->minimum_charge) }}" placeholder="150">
                    @error('minimum_charge') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-lg border border-zinc-800 bg-zinc-900 p-4">
                <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-zinc-300 text-yellow-400 focus:ring-yellow-400" @checked(old('is_active', $template->is_active))>
                <span>
                    <span class="block font-black">Active template</span>
                    <span class="block text-sm text-zinc-400">Only active templates appear on the calculator page.</span>
                </span>
            </label>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="btn-primary">{{ $template->exists ? 'Save Changes' : 'Create Template' }}</button>
                <a href="{{ route('templates.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
@endsection
