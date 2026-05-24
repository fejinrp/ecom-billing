@props([
    'headers' => [],
    'collection' => null,
    'type' => 'glass', // 'glass' or 'card'
    'minWidth' => '900px'
])

@php
$cardClass = $type === 'card' 
    ? 'p-6 rounded-3xl bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 shadow-md dark:shadow-none space-y-4' 
    : 'glassmorphism rounded-2xl overflow-hidden shadow-2xl';

$tableContainerClass = $type === 'card'
    ? 'responsive-table-container scrollbar-thin rounded-2xl border border-slate-200 dark:border-slate-800'
    : 'w-full overflow-visible lg:overflow-x-auto responsive-table-container scrollbar-thin';

$tableClass = $type === 'card'
    ? 'w-full text-left text-sm text-slate-600 dark:text-slate-300 min-w-0 lg:min-w-[var(--table-min-width)] block lg:table'
    : 'w-full text-left border-collapse min-w-0 lg:min-w-[var(--table-min-width)] block lg:table';

$theadClass = $type === 'card'
    ? 'bg-slate-50 dark:bg-slate-950 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 hidden lg:table-header-group'
    : 'bg-slate-50/80 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800/80 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-header-group';

$tbodyClass = $type === 'card'
    ? 'divide-y divide-slate-200 dark:divide-slate-850 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-200/60 dark:lg:divide-slate-800/40 p-4 lg:p-0'
    : 'divide-y divide-slate-200 dark:divide-slate-800/40 text-sm text-slate-600 dark:text-slate-300 block lg:table-row-group w-full space-y-4 lg:space-y-0 lg:divide-y lg:divide-slate-200/60 dark:lg:divide-slate-800/40 p-4 lg:p-0';
@endphp

<div class="{{ $cardClass }}">
    <div class="{{ $tableContainerClass }}">
        <table class="{{ $tableClass }}" style="--table-min-width: {{ $minWidth }}">
            @if(count($headers) > 0)
                <thead class="{{ $theadClass }}">
                    <tr class="lg:table-row">
                        @foreach($headers as $header)
                            <th class="px-6 py-4 {{ isset($header['align']) && $header['align'] === 'right' ? 'text-right' : (isset($header['align']) && $header['align'] === 'center' ? 'text-center' : '') }}">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody class="{{ $tbodyClass }}">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if($collection && is_object($collection) && method_exists($collection, 'hasPages') && $collection->hasPages())
        <div class="{{ $type === 'card' ? 'pt-2' : 'px-6 py-4 bg-slate-50/50 dark:bg-slate-900/40 border-t border-slate-200 dark:border-slate-800/80' }}">
            {{ $collection->links() }}
        </div>
    @endif
</div>

