@extends('layouts.app', ['title' => 'Templates'])

@section('content')
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-yellow-300">Templates</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">Calculation templates</h1>
            <p class="mt-2 text-zinc-400">Manage formulas, multipliers, minimum charges, and active status.</p>
        </div>
        <a href="{{ route('templates.create') }}" class="btn-primary">Add Template</a>
    </div>

    <section class="card overflow-hidden">
        @if ($templates->isEmpty())
            <div class="p-8 text-center">
                <p class="text-lg font-black">No templates yet</p>
                <p class="mt-2 text-sm text-zinc-400">Create your first template to start calculating.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Multiplier</th>
                        <th>Minimum</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($templates as $template)
                        <tr>
                            <td class="font-black">{{ $template->name }}</td>
                            <td>{{ $formulaTypes[$template->formula_type] ?? $template->formula_type }}</td>
                            <td class="max-w-md text-zinc-400">{{ $template->description ?: '-' }}</td>
                            <td class="font-bold">{{ rtrim(rtrim(number_format($template->multiplier, 6), '0'), '.') }}</td>
                            <td>{{ $template->minimum_charge ? 'RM'.number_format($template->minimum_charge, 2) : '-' }}</td>
                            <td>
                                <span class="{{ $template->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('templates.edit', $template) }}" class="btn-small">Edit</a>
                                    <form method="POST" action="{{ route('templates.destroy', $template) }}" onsubmit="return confirm('Delete this template?')">
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
                {{ $templates->links() }}
            </div>
        @endif
    </section>
@endsection
