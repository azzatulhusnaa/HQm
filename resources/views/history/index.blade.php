@extends('layouts.app', ['title' => 'Calculation History'])

@section('content')
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-yellow-300">History</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-white sm:text-4xl">Calculation history</h1>
        <p class="mt-2 text-zinc-400">A simple log of calculated totals.</p>
    </div>

    <section class="card overflow-hidden">
        @if ($histories->isEmpty())
            <div class="p-8 text-center">
                <p class="text-lg font-black">No history yet</p>
                <p class="mt-2 text-sm text-zinc-400">Run a calculation and it will be saved here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Template</th>
                        <th>Breakdown</th>
                        <th>Width</th>
                        <th>Height</th>
                        <th>Quantity</th>
                        <th>Multiplier</th>
                        <th class="text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($histories as $history)
                        <tr>
                            <td class="whitespace-nowrap text-zinc-400">{{ $history->created_at->format('d M Y, h:i A') }}</td>
                            <td class="font-bold">{{ $history->template_name }}</td>
                            <td class="max-w-sm text-zinc-400">{{ $history->breakdown[0]['value'] ?? $history->formula_type }}</td>
                            <td>{{ number_format($history->width, 2) }}</td>
                            <td>{{ number_format($history->height, 2) }}</td>
                            <td>{{ number_format($history->amount, 2) }}</td>
                            <td>{{ rtrim(rtrim(number_format($history->multiplier, 6), '0'), '.') }}</td>
                            <td class="text-right font-black">{{ number_format($history->total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-800 px-6 py-4">
                {{ $histories->links() }}
            </div>
        @endif
    </section>
@endsection
