@extends('layouts.admin', ['title' => $title])

@section('content')
<div class="space-y-6">
    <x-admin.header :title="$title" :description="$description">
        <x-slot:action>
            <x-admin.button href="{{ route('admin.sales.create') }}" icon="fa-solid fa-plus text-xs">
                Record New Sale
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Search & Filter Bar -->
    <x-admin.search-bar :action="route('admin.sales.index') . '?view=list'" placeholder="Search by Customer, Mobile, Bill # or Product Name...">
        <x-slot:info>
            <span>Showing {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} of {{ $items->total() }} recorded items</span>
        </x-slot:info>
    </x-admin.search-bar>

    <!-- Responsive Items Table -->
    @php
    $tableHeaders = [
        ['label' => 'Bill #'],
        ['label' => 'Date'],
        ['label' => 'Customer'],
        ['label' => 'Mobile'],
        ['label' => 'Product Name'],
        ['label' => 'Sl.No', 'align' => 'center'],
        ['label' => 'HSN/SAC', 'align' => 'center'],
        ['label' => 'GST %', 'align' => 'center'],
        ['label' => 'Qty', 'align' => 'center'],
        ['label' => 'Price', 'align' => 'right'],
        ['label' => 'Total', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$items" type="glass" minWidth="1100px">
        @forelse ($items as $index => $item)
            <tr class="hover:bg-slate-900/20 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20 lg:transition-all">
                
                <!-- Bill # Badge / Mobile Card Header -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-indigo-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">Bill #</span>
                    <span>#{{ str_pad($item->order->morder_id ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                </td>

                <!-- Date -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Date</span>
                    <span class="font-medium whitespace-nowrap">{{ $item->order ? date('d-m-Y', strtotime($item->order->order_date)) : 'N/A' }}</span>
                </td>

                <!-- Customer Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Customer</span>
                    <span class="font-semibold text-slate-200">{{ $item->order->client_name ?? 'N/A' }}</span>
                </td>

                <!-- Contact -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none text-xs font-medium text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mobile</span>
                    <span>{{ ($item->order && $item->order->mobile) ? $item->order->mobile : 'N/A' }}</span>
                </td>

                <!-- Product Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-2 block lg:table-cell lg:col-span-none font-medium text-slate-200">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Product Name</span>
                    <span>{{ $item->product ? $item->product->productname : $item->product_id }}</span>
                </td>

                <!-- Sl.No -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center text-xs text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sl.No</span>
                    <span>{{ $item->slno }}</span>
                </td>

                <!-- HSN/SAC -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center text-xs text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">HSN/SAC</span>
                    <span>{{ $item->hsnsan ?: '-' }}</span>
                </td>

                <!-- GST % -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center font-semibold text-indigo-400 text-xs">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">GST %</span>
                    <span>{{ $item->gst }}%</span>
                </td>

                <!-- Qty -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-center font-semibold text-slate-300">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Qty</span>
                    <span>{{ $item->qty }} {{ $item->unit }}</span>
                </td>

                <!-- Price -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-medium text-slate-400">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Price</span>
                    <span>Rs. {{ number_format($item->rate, 2) }}</span>
                </td>

                <!-- Total -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right font-bold text-slate-100">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total</span>
                    <span>Rs. {{ number_format($item->total, 2) }}</span>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="11" class="px-6 py-12 text-center text-slate-500 font-medium block lg:table-cell w-full">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fa-solid fa-receipt text-4xl text-slate-600 animate-bounce"></i>
                        <span>No sales items found matching search criteria.</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
