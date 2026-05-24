@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Header Section -->
    <x-admin.header 
        title="Manage User Settings" 
        description="Configure module-level access and operations permissions for active executive and administrator accounts. (ref: admin/usersetting.php)" 
        glass="false">
    </x-admin.header>

    <!-- Matrix Table Container -->
    <div class="bg-white dark:bg-slate-900/40 backdrop-blur-md border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800/60 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-table-cells text-indigo-600 dark:text-indigo-400"></i>
                Permissions Matrix Control
            </h3>
            <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-1 rounded font-medium">Auto-Saving Enabled</span>
        </div>

        <div class="responsive-table-container scrollbar-thin overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[2000px]">
                <thead>
                    <!-- Section Groups -->
                    <tr class="bg-slate-50 dark:bg-slate-900/80 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-slate-200 dark:border-slate-800">
                        <th colspan="3" class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 text-center">User details</th>
                        <th colspan="3" class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 text-center bg-indigo-50/50 dark:bg-indigo-500/5 text-indigo-600 dark:text-indigo-300">Brand & Category</th>
                        <th colspan="7" class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 text-center">Product</th>
                        <th colspan="7" class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 text-center bg-indigo-50/50 dark:bg-indigo-500/5 text-indigo-600 dark:text-indigo-300">Offline Sales</th>
                        <th colspan="4" class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 text-center">Online Sales</th>
                        <th colspan="4" class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 text-center bg-indigo-50/50 dark:bg-indigo-500/5 text-indigo-600 dark:text-indigo-300">Expenses</th>
                        <th colspan="7" class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 text-center">Report</th>
                        <th colspan="5" class="px-4 py-3 text-center bg-indigo-50/50 dark:bg-indigo-500/5 text-indigo-600 dark:text-indigo-300">Setting</th>
                    </tr>
                    <!-- Columns -->
                    <tr class="bg-white dark:bg-slate-950 text-[10px] font-bold text-slate-600 dark:text-slate-500 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <!-- User details -->
                        <th class="px-4 py-3 text-center border-r border-slate-200 dark:border-slate-800/60" style="width: 50px;">No</th>
                        <th class="px-4 py-3 border-r border-slate-200 dark:border-slate-800/60 min-w-[120px]">UName</th>
                        <th class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 min-w-[100px] text-center">Role</th>
                        <!-- Brand & Category -->
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Category">Cat</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Sub Category">SCat</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800 bg-indigo-50/50 dark:bg-indigo-500/5" title="Brand">Brand</th>
                        <!-- Product -->
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Products">Prod</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Manage Product">MProd</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Purchase">Purc</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Manage Purchase">MPurc</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Add Stock">AStock</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Stock List">SList</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800" title="Price Search">SPrice</th>
                        <!-- Offline Sales -->
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Customer Invoice">CInv</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Manage Invoice">MInv</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Sales List Cust">SList</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Quotation">Quot</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Manage Quotation">MQuot</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Estimation">Estm</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800 bg-indigo-50/50 dark:bg-indigo-500/5" title="Manage Estimation">MEstm</th>
                        <!-- Online Sales -->
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Orders">Ord</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Sending Orders">SOrd</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Delivery Orders">DOrd</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800" title="Cancel Orders">COrd</th>
                        <!-- Expenses -->
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Expenses Name">EName</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Expenses">Expen</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Agent">Agent</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800 bg-indigo-50/50 dark:bg-indigo-500/5" title="Agent Pay">APay</th>
                        <!-- Report -->
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="All Report">All</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Billwise Report">Billw</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Sales Report">Sales</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Pending Report">Pend</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Stock Report">Stock</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40" title="Pay History">PHist</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800" title="To Excel">Excel</th>
                        <!-- Setting -->
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Add User">AUser</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="User Setting">USett</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Customer Setting">CSett</th>
                        <th class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 bg-indigo-50/50 dark:bg-indigo-500/5" title="Backup">Back</th>
                        <th class="px-2 py-3 text-center bg-indigo-50/50 dark:bg-indigo-500/5" title="Restore">Rest</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                    @forelse($userchecks as $index => $uc)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/20 transition-all text-sm text-slate-700 dark:text-slate-300">
                            <!-- No -->
                            <td class="px-4 py-3 text-center border-r border-slate-200 dark:border-slate-800/60 font-mono text-xs text-slate-500">
                                #{{ $index + 1 }}
                            </td>
                            <!-- Username -->
                            <td class="px-4 py-3 border-r border-slate-200 dark:border-slate-800/60 font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $uc->username }}
                            </td>
                            <!-- Role -->
                            <td class="px-4 py-3 border-r border-slate-200 dark:border-slate-800 text-center text-xs">
                                <select 
                                    class="perm-checkbox bg-transparent border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded px-2 py-1 text-xs focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer"
                                    data-id="{{ $uc->id }}"
                                    data-column="section">
                                    <option value="1" {{ $uc->section == 1 ? 'selected' : '' }}>Admin</option>
                                    <option value="2" {{ $uc->section == 2 ? 'selected' : '' }}>Exec</option>
                                </select>
                            </td>

                            <!-- Permission Matrix Checkboxes -->
                            @php
                            $permissions = [
                                ['col' => 'cat', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'scat', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'brand', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'prod', 'bg' => ''],
                                ['col' => 'mprod', 'bg' => ''],
                                ['col' => 'purc', 'bg' => ''],
                                ['col' => 'mpurc', 'bg' => ''],
                                ['col' => 'astock', 'bg' => ''],
                                ['col' => 'slist', 'bg' => ''],
                                ['col' => 'sprice', 'bg' => ''],
                                ['col' => 'cinv', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'minv', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'linvc', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'quot', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'mquot', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'estm', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'mestm', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'ord', 'bg' => ''],
                                ['col' => 'sord', 'bg' => ''],
                                ['col' => 'dord', 'bg' => ''],
                                ['col' => 'cord', 'bg' => ''],
                                ['col' => 'expen', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'expd', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'agent', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'apay', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'areport', 'bg' => ''],
                                ['col' => 'breport', 'bg' => ''],
                                ['col' => 'sreport', 'bg' => ''],
                                ['col' => 'preport', 'bg' => ''],
                                ['col' => 'stockr', 'bg' => ''],
                                ['col' => 'phistory', 'bg' => ''],
                                ['col' => 'excel', 'bg' => ''],
                                ['col' => 'auser', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'usett', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'csett', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'backup', 'bg' => 'bg-indigo-500/5'],
                                ['col' => 'restore', 'bg' => 'bg-indigo-500/5']
                            ];
                            @endphp

                            @foreach($permissions as $perm)
                                <td class="px-2 py-3 text-center border-r border-slate-200 dark:border-slate-800/40 {{ $perm['bg'] }}">
                                    <input type="checkbox" 
                                           class="perm-checkbox w-4 h-4 text-indigo-600 bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-800 rounded focus:ring-indigo-500/40 focus:ring-2 focus:ring-offset-white dark:focus:ring-offset-slate-950 cursor-pointer transition-all"
                                           data-id="{{ $uc->id }}"
                                           data-column="{{ $perm['col'] }}"
                                           {{ $uc->{$perm['col']} == 1 ? 'checked' : '' }}>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="40" class="px-6 py-8 text-center text-slate-500 font-medium">No active users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const initPermissions = () => {
        const checkboxes = document.querySelectorAll('.perm-checkbox');
        checkboxes.forEach(box => {
            box.addEventListener('change', async (e) => {
                const id = e.target.dataset.id;
                const column = e.target.dataset.column;
                let value;
                if (e.target.tagName === 'SELECT') {
                    value = e.target.value;
                } else {
                    value = e.target.checked ? 1 : 0;
                }
                
                // Visual pending feedback
                e.target.style.opacity = '0.4';
                e.target.disabled = true;

                try {
                    const response = await fetch('{{ route("admin.usersettings.update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ id, column, value })
                    });
                    
                    const data = await response.json();
                    if (!data.success) {
                        if (e.target.tagName !== 'SELECT') e.target.checked = !e.target.checked; // Rollback
                    }
                } catch (error) {
                    console.error('Failed to sync permission state', error);
                    if (e.target.tagName !== 'SELECT') e.target.checked = !e.target.checked; // Rollback
                } finally {
                    e.target.style.opacity = '1';
                    e.target.disabled = false;
                }
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPermissions);
    } else {
        initPermissions();
    }
</script>
@endsection
