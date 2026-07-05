@extends('layouts.admin')

@section('title', 'Lucky Draw — Settings')

@section('content')
<div class="space-y-6" x-data="drawSettings()">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">Lucky Draw Settings</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Configure draw categories, thresholds, batch sizes, and prize amounts.</p>
        </div>
        <a href="{{ route('admin.lucky_draw.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
            <i class="fa-solid fa-arrow-left text-xs"></i> Draw Board
        </a>
    </div>

    {{-- ── Flash Messages ───────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-700/40 dark:bg-green-900/20 dark:text-green-300">
            <i class="fa-solid fa-circle-check mt-0.5 shrink-0 text-green-500"></i>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-700/40 dark:bg-red-900/20 dark:text-red-300">
            <i class="fa-solid fa-circle-xmark mt-0.5 shrink-0 text-red-500"></i>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ── Settings Form ────────────────────────────────────────────────────── --}}
    <form action="{{ route('admin.lucky_draw.settings.update') }}" method="POST" id="settings-form">
        @csrf
        <div class="space-y-4">

            {{-- Existing categories --}}
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-indigo-500"></i>
                            <h3 class="font-semibold text-slate-700 dark:text-slate-200" x-text="row.category_label || 'New Category'"></h3>
                            <span x-show="!row.is_active" class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-500 dark:bg-red-900/20">Inactive</span>
                        </div>
                        <button type="button" @click="removeRow(index)"
                                class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fa-solid fa-trash-can mr-1"></i> Remove
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                        {{-- Category Key --}}
                        <div class="col-span-1">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Key</label>
                            <input type="text"
                                   :name="'settings[' + index + '][category_key]'"
                                   x-model="row.category_key"
                                   placeholder="e.g. bronze"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-mono text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        </div>

                        {{-- Label --}}
                        <div class="col-span-1">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Label</label>
                            <input type="text"
                                   :name="'settings[' + index + '][category_label]'"
                                   x-model="row.category_label"
                                   placeholder="e.g. Bronze"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        </div>

                        {{-- Min Amount --}}
                        <div class="col-span-1">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Min ₹</label>
                            <input type="number" step="0.01" min="0"
                                   :name="'settings[' + index + '][min_amount]'"
                                   x-model="row.min_amount"
                                   placeholder="0"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        </div>

                        {{-- Max Amount --}}
                        <div class="col-span-1">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Max ₹ <span class="normal-case text-slate-400">(blank = no limit)</span></label>
                            <input type="number" step="0.01" min="0"
                                   :name="'settings[' + index + '][max_amount]'"
                                   x-model="row.max_amount"
                                   placeholder="∞"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        </div>

                        {{-- Batch Size --}}
                        <div class="col-span-1">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Batch Size</label>
                            <input type="number" min="1"
                                   :name="'settings[' + index + '][batch_size]'"
                                   x-model="row.batch_size"
                                   placeholder="40"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        </div>

                        {{-- Prize Amount --}}
                        <div class="col-span-1">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Prize ₹</label>
                            <input type="number" step="0.01" min="0"
                                   :name="'settings[' + index + '][prize_amount]'"
                                   x-model="row.prize_amount"
                                   placeholder="1000"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        </div>
                    </div>

                    {{-- Active Toggle --}}
                    <div class="mt-3 flex items-center gap-2">
                        <input type="hidden" :name="'settings[' + index + '][is_active]'" value="0">
                        <input type="checkbox" :id="'active_' + index"
                               :name="'settings[' + index + '][is_active]'"
                               x-model="row.is_active"
                               value="1"
                               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label :for="'active_' + index" class="text-sm text-slate-600 dark:text-slate-400 cursor-pointer">
                            Active — include this category in the draw board
                        </label>
                    </div>
                </div>
            </template>

            {{-- Add Category Button --}}
            <button type="button" @click="addRow"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-300 bg-white py-4 text-sm font-semibold text-slate-500 transition-colors hover:border-indigo-400 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 dark:hover:border-indigo-500">
                <i class="fa-solid fa-plus"></i> Add New Category
            </button>

        </div>

        {{-- Save --}}
        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('admin.lucky_draw.index') }}"
               class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-700 active:scale-95 transition-all">
                <i class="fa-solid fa-floppy-disk"></i> Save Settings
            </button>
        </div>
    </form>

    {{-- ── Danger Zone: Delete Categories ─────────────────────────────────── --}}
    @if($settings->isNotEmpty())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 dark:border-red-700/30 dark:bg-red-900/10">
            <h3 class="mb-3 flex items-center gap-2 text-sm font-bold text-red-700 dark:text-red-400">
                <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone — Permanently Delete Category
            </h3>
            <p class="mb-4 text-xs text-red-600 dark:text-red-400">
                Deleting a category is irreversible. Categories with existing draw history cannot be deleted.
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach($settings as $s)
                    <form action="{{ route('admin.lucky_draw.settings.destroy', $s->id) }}" method="POST"
                          onsubmit="return confirm('Delete category \'{{ $s->category_label }}\'? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 dark:border-red-700/30 dark:bg-red-900/10 dark:text-red-400 dark:hover:bg-red-900/20">
                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                            {{ $s->category_label }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

</div>

@php
    $settingsJson = $settings->map(fn ($s) => [
        'category_key'   => $s->category_key,
        'category_label' => $s->category_label,
        'min_amount'     => $s->min_amount,
        'max_amount'     => $s->max_amount,
        'batch_size'     => $s->batch_size,
        'prize_amount'   => $s->prize_amount,
        'is_active'      => (bool) $s->is_active,
    ])->values();
@endphp


<script>
function drawSettings() {
    return {
        rows: @json($settingsJson),

        addRow() {
            this.rows.push({
                category_key:   '',
                category_label: '',
                min_amount:     0,
                max_amount:     '',
                batch_size:     40,
                prize_amount:   1000,
                is_active:      true,
            });
        },

        removeRow(index) {
            if (confirm('Remove this category row?')) {
                this.rows.splice(index, 1);
            }
        }
    };
}
</script>

@endsection
