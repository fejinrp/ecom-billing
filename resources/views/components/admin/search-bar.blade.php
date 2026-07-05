@props([
    'action'      => '',
    'placeholder' => 'Search...',
])

<form method="GET" action="{{ $action }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
    {{-- Slot: hidden inputs or extra filters passed from the parent --}}
    {{ $slot }}

    {{-- Search input with icon prefix + clear button --}}
    <div class="relative flex-1">
        <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-slate-500">
            <i class="fa-solid fa-magnifying-glass text-sm"></i>
        </span>
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-10 py-2.5 text-sm text-slate-300 placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all"
        >
        @if(request('search'))
            <a href="{{ $action }}" class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-rose-400 transition-colors" title="Clear search">
                <i class="fa-solid fa-xmark text-sm"></i>
            </a>
        @endif
    </div>

    {{-- Submit --}}
    <button type="submit" class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-lg shadow-indigo-600/10 hover:shadow-indigo-600/25 transition-all">
        <i class="fa-solid fa-magnifying-glass text-xs"></i>
        Search
    </button>

    {{-- Optional info slot: e.g. "Showing X-Y of Z records" --}}
    @if(isset($info))
        <p class="text-xs text-slate-500 sm:ml-1 self-center whitespace-nowrap">
            {{ $info }}
        </p>
    @endif
</form>
