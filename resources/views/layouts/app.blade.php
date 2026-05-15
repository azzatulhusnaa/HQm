<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Calculator System' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-black text-zinc-100 antialiased">
    <div class="min-h-screen">
        <header class="border-b border-zinc-800 bg-zinc-950">
            <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <a href="{{ route('calculator.index') }}" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-yellow-400 text-xl font-black text-black shadow-sm">C</span>
                    <span>
                        <span class="block text-lg font-black tracking-tight">Calculator System</span>
                        <span class="block text-sm font-medium text-zinc-400">Simple template-based totals</span>
                    </span>
                </a>

                <nav class="flex flex-wrap gap-2">
                    <a class="nav-link {{ request()->routeIs('calculator.*') ? 'nav-link-active' : '' }}" href="{{ route('calculator.index') }}">Calculator</a>
                    <a class="nav-link {{ request()->routeIs('templates.*') ? 'nav-link-active' : '' }}" href="{{ route('templates.index') }}">Templates</a>
                    <a class="nav-link {{ request()->routeIs('paper_size-options.*') ? 'nav-link-active' : '' }}" href="{{ route('paper_size-options.index') }}">Paper Sizes</a>
                    <a class="nav-link {{ request()->routeIs('history.*') ? 'nav-link-active' : '' }}" href="{{ route('history.index') }}">History</a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-yellow-500/60 bg-yellow-400/10 px-5 py-4 text-sm font-semibold text-yellow-100">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <button
        type="button"
        id="converter-toggle"
        class="fixed right-0 top-1/2 z-40 -translate-y-1/2 rounded-l-lg border border-r-0 border-yellow-400 bg-yellow-400 px-2 py-3 text-xs font-black text-black shadow-lg transition hover:bg-yellow-300"
        aria-controls="converter-drawer"
        aria-expanded="false"
        title="Unit converter"
    >
        UNIT
    </button>

    <aside
        id="converter-drawer"
        class="fixed right-0 top-1/2 z-50 w-64 -translate-y-1/2 translate-x-full rounded-l-lg border border-zinc-800 bg-zinc-950 p-4 text-zinc-100 shadow-2xl transition-transform duration-200"
        aria-hidden="true"
    >
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 class="text-sm font-black text-white">Unit Converter</h2>
            <button type="button" id="converter-close" class="rounded-md px-2 py-1 text-sm font-black text-zinc-400 hover:bg-zinc-900 hover:text-yellow-300" aria-label="Close converter">x</button>
        </div>

        <div class="space-y-3">
            <div>
                <label class="label" for="converter-value">Value</label>
                <input id="converter-value" type="number" step="0.0001" class="input py-2 text-sm" placeholder="0">
            </div>

            <div class="grid grid-cols-[1fr_auto_1fr] items-end gap-2">
                <div>
                    <label class="label" for="converter-from">From</label>
                    <select id="converter-from" class="input py-2 text-sm">
                        <option value="mm">mm</option>
                        <option value="cm">cm</option>
                        <option value="m">m</option>
                        <option value="inch">inch</option>
                        <option value="feet">feet</option>
                        <option value="pt">pt</option>
                    </select>
                </div>

                <button type="button" id="converter-swap" class="mb-px rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm font-black text-yellow-300 hover:border-yellow-400" title="Swap units">=</button>

                <div>
                    <label class="label" for="converter-to">To</label>
                    <select id="converter-to" class="input py-2 text-sm">
                        <option value="cm">cm</option>
                        <option value="mm">mm</option>
                        <option value="m">m</option>
                        <option value="inch">inch</option>
                        <option value="feet">feet</option>
                        <option value="pt">pt</option>
                    </select>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-800 bg-zinc-900 p-3">
                <p class="text-xs font-bold uppercase text-zinc-400">Result</p>
                <p id="converter-result" class="mt-1 break-words text-lg font-black text-yellow-300">0</p>
            </div>
        </div>
    </aside>

    <script>
        (() => {
            const toggle = document.getElementById('converter-toggle');
            const drawer = document.getElementById('converter-drawer');
            const close = document.getElementById('converter-close');
            const value = document.getElementById('converter-value');
            const from = document.getElementById('converter-from');
            const to = document.getElementById('converter-to');
            const swap = document.getElementById('converter-swap');
            const result = document.getElementById('converter-result');
            const mmPerUnit = {
                mm: 1,
                cm: 10,
                m: 1000,
                inch: 25.4,
                feet: 304.8,
                pt: 25.4 / 72,
            };

            function setOpen(isOpen) {
                drawer.classList.toggle('translate-x-full', !isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            }

            function formatNumber(number) {
                if (!Number.isFinite(number)) {
                    return '0';
                }

                return Number.parseFloat(number.toFixed(6)).toString();
            }

            function convert() {
                const input = Number.parseFloat(value.value || '0');
                const converted = (input * mmPerUnit[from.value]) / mmPerUnit[to.value];
                result.textContent = `${formatNumber(converted)} ${to.value}`;
            }

            toggle.addEventListener('click', () => {
                setOpen(toggle.getAttribute('aria-expanded') !== 'true');
            });

            close.addEventListener('click', () => setOpen(false));
            value.addEventListener('input', convert);
            from.addEventListener('change', convert);
            to.addEventListener('change', convert);
            swap.addEventListener('click', () => {
                const currentFrom = from.value;
                from.value = to.value;
                to.value = currentFrom;
                convert();
            });

            convert();
        })();
    </script>
</body>
</html>
