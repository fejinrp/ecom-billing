<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        // Anti-flash theme loader: read theme from localStorage or OS settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Product Barcodes - MTL Mart</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Print and Sheet layout configurations */
        @page {
            margin: 0;
        }
        @media print {
            body {
                background: white;
                color: black;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                display: block !important;
            }
            .sheet-page {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                background: white !important;
                color: black !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        .sheet-page {
            width: 101.6mm;
            height: 34.4mm;
            box-sizing: border-box;
            background: white;
            color: black;
            padding: 3mm 2.5mm;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        .label-tag {
            width: 32mm; /* ~ 33.3% to account for gaps/borders */
            height: 28.4mm;
            box-sizing: border-box;
            padding: 1mm 1.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-[#0b0f19] text-slate-800 dark:text-slate-300 print:bg-white print:text-black">

    <!-- Floating Top Bar (Hidden on actual print) -->
    <div class="no-print bg-white dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-50 px-6 py-4 flex items-center justify-between shadow-md dark:shadow-2xl">
        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('admin.products.barcode') }}" variant="secondary" icon="fa-solid fa-arrow-left" class="px-4 py-2 rounded-xl text-sm font-semibold">
                Configure Sheet
            </x-admin.button>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-400 border border-slate-200 dark:border-slate-700/50 rounded-full font-mono">
                Generating: {{ $qty }} tags for {{ $pcode }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <x-admin.button onclick="window.print()" variant="indigo" icon="fa-solid fa-print" class="px-5 py-2.5 rounded-xl">
                Print Barcode Sheet
            </x-admin.button>
        </div>
    </div>

    <!-- Printable Canvas Area -->
    <div class="max-w-xl mx-auto my-8 p-4 no-print flex flex-col items-center gap-6">
        <div class="p-3 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-xs text-indigo-700 dark:text-indigo-300 text-center leading-relaxed">
            <i class="fa-solid fa-circle-info mr-1 text-sm"></i>
            Below is a live rendering preview of the barcode sheet pages. Click **Print Barcode Sheet** above or hit **Cmd+P** to open the browser print manager. Make sure margins are set to **None** in printer settings.
        </div>
    </div>

    <!-- Render label sticker sheet chunks (3 columns per page) -->
    <div class="print-container flex flex-col items-center justify-center gap-4 print:gap-0">
        @php
            // Group tags into sets of 3 labels per physical 101.6mm x 34.4mm landscape sheet
            $rows = ceil($qty / 3);
        @endphp

        @for($r = 0; $r < $rows; $r++)
            <div class="sheet-page shadow-2xl rounded-2xl print:rounded-none border border-slate-800/20 print:border-none divide-x divide-slate-100/50 print:divide-slate-350">
                @for($col = 0; $col < 3; $col++)
                    @php
                        $index = ($r * 3) + $col;
                    @endphp

                    @if($index < $qty)
                        <!-- Label Tag element -->
                        <div class="label-tag font-mono text-black">
                            <!-- Top row: Product Code & MRP -->
                            <div class="flex justify-between items-center w-full select-none">
                                <span class="text-[9px] font-extrabold uppercase tracking-tight">{{ $pcode }}</span>
                                <span class="text-[9px] font-extrabold">MRP:{{ $mrp }}</span>
                            </div>

                            <!-- Middle row: Customer Price & Obfuscated Dealer Price -->
                            <div class="text-[10px] font-black tracking-tight leading-none mt-0.5">
                                ₹{{ $cprice }} &nbsp;{{ $dealerObfuscation }}{{ $dprice }}
                            </div>

                            <!-- Vector SVG Barcode markup -->
                            <div class="w-full flex items-center justify-center mt-1 scale-x-95">
                                {!! $barcodeSvg !!}
                            </div>

                            <!-- Bottom row: Product Name & Obfuscated S-Dealer Price -->
                            <div class="flex justify-between items-center w-full mt-0.5 select-none">
                                <span class="text-[8px] font-extrabold uppercase tracking-tight truncate max-w-[20mm]">{{ $pname }}</span>
                                <span class="text-[8px] font-extrabold">{{ $sdealerObfuscation }}{{ $sdprice }}</span>
                            </div>
                        </div>
                    @else
                        <!-- Empty slot placeholder to maintain 3-column structural layout alignment -->
                        <div class="label-tag opacity-0"></div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>

</body>
</html>
