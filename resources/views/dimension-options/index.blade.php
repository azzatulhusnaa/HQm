@extends('layouts.app', ['title' => $title.' Options'])

@section('content')
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-yellow-300">Manage</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">{{ $title }} options</h1>
            <p class="mt-2 text-zinc-400">Add, edit, delete, or disable options used by the calculator.</p>
        </div>
        <a href="{{ route($type.'-options.create') }}" class="btn-primary">Add {{ $title }}</a>
    </div>

    <section class="card overflow-hidden">
        @if ($options->isEmpty())
            <div class="p-8 text-center">
                <p class="text-lg font-black">No options yet</p>
                <p class="mt-2 text-sm text-zinc-400">Create the first option for this list.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Label</th>
                        @if ($type === 'paper_size')
                            <th>Size</th>
                            <th>Base Price</th>
                            <th>Included Sheets</th>
                        @else
                            <th>Value</th>
                        @endif
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($options as $option)
                        <tr>
                            <td class="font-black">{{ $option->label }}</td>
                            @if ($type === 'paper_size')
                                <td>{{ number_format($option->width, 2) }} x {{ number_format($option->height, 2) }}</td>
                                <td>RM{{ number_format($option->base_price, 2) }}</td>
                                <td>{{ number_format($option->included_quantity ?: 1000) }}</td>
                            @else
                                <td>{{ number_format($option->value, 2) }}</td>
                            @endif
                            <td>
                                <span class="{{ $option->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $option->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route($type.'-options.edit', $option) }}" class="btn-small">Edit</a>
                                    <form method="POST" action="{{ route($type.'-options.destroy', $option) }}" onsubmit="return confirm('Delete this option?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-small-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-800 px-6 py-4">
                {{ $options->links() }}
            </div>
        @endif
    </section>
@endsection
