@extends('layouts.storefront')

@section('content')
    @php
        $paymentMethod = 'Cash';
        if ($order->payment_type == 1) {
            $paymentMethod = 'Cheque';
        } elseif ($order->payment_type == 3) {
            $paymentMethod = 'Online / UPI';
        } elseif ($order->payment_type == 4) {
            $paymentMethod = 'Debit Card';
        }
    @endphp

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-12">
        <div class="text-center mb-8 sm:mb-10">
            <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-500 shadow-sm">
                <i class="fa-solid fa-circle-check text-4xl"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black uppercase tracking-tight text-slate-900 dark:text-white">
                Order Authorized
            </h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm sm:text-base leading-relaxed text-slate-600 dark:text-slate-400">
                Your purchase has been committed successfully. The order view and the print view now share the same storefront structure for a consistent customer experience.
            </p>
        </div>

        <div class="overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.08)] dark:border-slate-800 dark:bg-slate-950/70 dark:shadow-[0_24px_80px_rgba(0,0,0,0.22)]">
            <div class="border-b border-slate-200/80 px-6 py-5 sm:px-8 dark:border-slate-800">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">
                            Order Reference
                        </p>
                        <p class="font-mono text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                            INVOICE: #{{ $order->morder_id }}
                        </p>
                    </div>
                    <div class="space-y-1 sm:text-right">
                        <p class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">
                            Logged Timestamp
                        </p>
                        <p class="font-mono text-sm font-semibold text-slate-600 dark:text-slate-300">
                            {{ date('d-m-Y', strtotime($order->order_date)) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-6 px-6 py-6 sm:px-8 sm:py-8">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900/60">
                        <p class="mb-3 text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">
                            Dispatch / Billing Target
                        </p>
                        <p class="text-base font-black uppercase text-slate-900 dark:text-white">
                            {{ $order->client_name }}
                        </p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-400 uppercase">
                            {{ $order->client_contact }}
                        </p>
                        @if ($order->mobile)
                            <p class="mt-3 text-xs font-black uppercase tracking-[0.25em] text-blue-600 dark:text-indigo-400 font-mono">
                                MOB: {{ $order->mobile }}
                            </p>
                        @endif
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900/60">
                        <p class="mb-3 text-[10px] font-black uppercase tracking-[0.35em] text-slate-500 dark:text-slate-400">
                            Financial Summary
                        </p>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4 text-slate-600 dark:text-slate-400">
                                <span>Payment Method</span>
                                <span class="text-right font-black uppercase text-slate-900 dark:text-white">
                                    {{ $paymentMethod }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-4 text-slate-600 dark:text-slate-400">
                                <span>Outstanding</span>
                                <span class="text-right font-mono font-black text-blue-600 dark:text-indigo-400">
                                    Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->grand_total) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-4 text-slate-600 dark:text-slate-400">
                                <span>Tax Allocation</span>
                                <span class="text-right font-mono font-semibold text-slate-900 dark:text-white">
                                    Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->gstn) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-3 font-black text-slate-900 dark:border-slate-800 dark:text-white">
                                <span class="text-[10px] uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">
                                    Total Inclusive
                                </span>
                                <span class="font-mono text-lg text-blue-600 dark:text-indigo-400">
                                    Rs. {{ \App\Helpers\NumberHelper::indianFormat($order->grand_total) }}
                                </span>
                            </div>
                        </div>
                    </section>
                </div>

                @if ($order->payment_type == 3)
                    <section class="rounded-2xl border border-blue-500/15 bg-blue-500/5 p-5 dark:border-indigo-500/20 dark:bg-indigo-500/10">
                        <h3 class="flex items-center gap-2 text-xs font-black uppercase tracking-[0.35em] text-blue-600 dark:text-indigo-400">
                            <i class="fa-solid fa-bank text-sm"></i>
                            Direct Bank Transfer Instruction
                        </h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                            Your checkout selection indicates direct bank remittance. Settle the total amount via UPI or internet banking to our corporate account. Once settled, dispatch processing is authorized.
                        </p>
                        <div class="mt-4 grid grid-cols-1 gap-4 rounded-2xl border border-slate-200 bg-white p-4 font-mono text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-300 sm:grid-cols-2">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400 font-sans">
                                    Corporate Remittance Target
                                </p>
                                <p class="font-black text-slate-900 dark:text-white">
                                    INDIAN OVERSEAS BANK
                                </p>
                                <p>Branch: Kuzhithurai</p>
                            </div>
                            <div class="space-y-1 sm:text-right">
                                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400 font-sans">
                                    Audit Credentials
                                </p>
                                <p>A/C No: <span class="font-black text-blue-600 dark:text-indigo-400">2869020000000349</span></p>
                                <p>IFSC: <span class="font-black text-blue-600 dark:text-indigo-400">IOBA0002869</span></p>
                            </div>
                        </div>
                    </section>
                @endif

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('storefront.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-xs font-black uppercase tracking-[0.25em] text-slate-700 transition-colors hover:border-slate-300 hover:text-slate-900 dark:border-slate-800 dark:bg-slate-900/70 dark:text-slate-300 dark:hover:border-slate-700 dark:hover:text-white sm:w-auto">
                        <i class="fa-solid fa-arrow-left mr-1.5"></i>
                        Return to Storefront
                    </a>

                    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                        <a href="{{ route('storefront.orders') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-center text-xs font-black uppercase tracking-[0.25em] text-slate-600 transition-colors hover:border-slate-300 hover:text-slate-900 dark:border-slate-800 dark:bg-slate-900/70 dark:text-slate-300 dark:hover:border-slate-700 dark:hover:text-white">
                            My Purchase History
                        </a>
                        <a href="{{ route('storefront.order_print', $order->order_id) }}?autoprint=1" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-center text-xs font-black uppercase tracking-[0.25em] text-white shadow-lg shadow-indigo-600/10 transition-transform hover:from-indigo-500 hover:to-purple-500 active:scale-95">
                            <i class="fa-solid fa-print"></i>
                            Print Invoice Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
