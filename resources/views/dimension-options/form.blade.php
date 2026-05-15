@extends('layouts.app', ['title' => $option->exists ? 'Edit '.$title : 'Add '.$title])

@section('content')
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-yellow-300">{{ $option->exists ? 'Edit' : 'Add' }} option</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">{{ $option->exists ? 'Update' : 'Create' }} {{ $title }}</h1>
    </div>

    <section class="card p-6">
        <form method="POST" action="{{ $option->exists ? route($type.'-options.update', $option) : route($type.'-options.store') }}" class="space-y-5">
            @csrf
            @if ($option->exists)
                @method('PUT')
            @endif

            <div>
                <label class="label" for="label">Label</label>
                <input id="label" name="label" class="input" value="{{ old('label', $option->label) }}" placeholder="{{ $type === 'paper_size' ? '25 x 37, 1000 sheets' : $title.' option' }}">
                @error('label') <p class="error">{{ $message }}</p> @enderror
            </div>

            @if ($type === 'paper_size')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="width">Width</label>
                        <input id="width" name="width" type="number" step="0.01" min="0" class="input" value="{{ old('width', $option->width) }}" placeholder="25">
                        @error('width') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label" for="height">Height</label>
                        <input id="height" name="height" type="number" step="0.01" min="0" class="input" value="{{ old('height', $option->height) }}" placeholder="37">
                        @error('height') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="base_price">Base price</label>
                        <input id="base_price" name="base_price" type="number" step="0.01" min="0" class="input" value="{{ old('base_price', $option->base_price) }}" placeholder="250">
                        @error('base_price') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label" for="included_quantity">Included sheets</label>
                        <input id="included_quantity" name="included_quantity" type="number" step="1" min="1" class="input" value="{{ old('included_quantity', $option->included_quantity ?: 1000) }}" placeholder="1000">
                        @error('included_quantity') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <input type="hidden" name="value" value="{{ old('width', $option->width ?: 0) }}">
            @else
                <div>
                    <label class="label" for="value">Value</label>
                    <input id="value" name="value" type="number" step="0.01" min="0" class="input" value="{{ old('value', $option->value) }}" placeholder="100">
                    @error('value') <p class="error">{{ $message }}</p> @enderror
                </div>
            @endif

            <label class="flex items-center gap-3 rounded-lg border border-zinc-800 bg-zinc-900 p-4">
                <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-zinc-300 text-yellow-400 focus:ring-yellow-400" @checked(old('is_active', $option->is_active))>
                <span>
                    <span class="block font-black">Active option</span>
                    <span class="block text-sm text-zinc-400">Only active options appear on the calculator page.</span>
                </span>
            </label>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="btn-primary">{{ $option->exists ? 'Save Changes' : 'Create Option' }}</button>
                <a href="{{ route($type.'-options.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
@endsection
