@extends('layouts.admin')

@section('content')
@php
    $adminUser = Auth::guard('admin')->user();
    $usercheck = $adminUser ? \App\Models\Usercheck::where('uid', $adminUser->user_id)->first() : null;
    $canRestore = $adminUser && ($adminUser->section == 1 || ($usercheck && $usercheck->restore == 1));
@endphp
<div class="space-y-8 animate-fadeIn" x-data="{ 
    showConfirmModal: false, 
    confirmAction: '', 
    confirmMethod: 'POST', 
    confirmTitle: '', 
    confirmText: '', 
    confirmBtnText: 'Confirm', 
    isDelete: false 
}">
    <!-- Header Section -->
    <x-admin.header 
        title="Database Backup Center" 
        description="Create secure gzipped SQL database backups, manage historical archives, and restore data. (ref: admin/myphpbackup.php)" 
        glass="false">
        <x-slot:action>
            <form method="POST" action="{{ route('admin.backups.create') }}">
                @csrf
                <x-admin.button type="submit" icon="fa-solid fa-download-blob">
                    Generate Backup
                </x-admin.button>
            </form>
        </x-slot:action>
    </x-admin.header>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Database Info -->
        <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 shadow-xl flex items-center gap-4">
            <div class="p-4 bg-indigo-500/10 text-indigo-400 rounded-2xl">
                <i class="fa-solid fa-database text-2xl"></i>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Database</span>
                <span class="block text-lg font-bold text-slate-200 mt-0.5 truncate max-w-[200px]">{{ $dbName }}</span>
            </div>
        </div>

        <!-- Total Tables -->
        <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 shadow-xl flex items-center gap-4">
            <div class="p-4 bg-orange-500/10 text-orange-400 rounded-2xl">
                <i class="fa-solid fa-table-cells text-2xl"></i>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Database Tables</span>
                <span class="block text-lg font-bold text-slate-200 mt-0.5">{{ $tableCount }} Active</span>
            </div>
        </div>

        <!-- Total Backups -->
        <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 shadow-xl flex items-center gap-4">
            <div class="p-4 bg-emerald-500/10 text-emerald-400 rounded-2xl">
                <i class="fa-solid fa-file-zipper text-2xl"></i>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Stored Backups</span>
                <span class="block text-lg font-bold text-slate-200 mt-0.5">{{ count($backups) }} Archives</span>
            </div>
        </div>
    </div>

    @if($canRestore)
    <!-- Upload & Restore Panel -->
    <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-slate-100 flex items-center gap-2">
            <i class="fa-solid fa-upload text-orange-400"></i>
            Upload & Restore Database (.sql or .sql.gz)
        </h3>
        <form id="upload-restore-form" method="POST" action="{{ route('admin.backups.upload_restore') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
            @csrf
            <div class="flex-1">
                <input type="file" name="backup_file" accept=".sql,.gz" required
                       class="block w-full text-sm text-slate-400
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-xl file:border-0
                              file:text-xs file:font-semibold
                              file:bg-indigo-600/10 file:text-indigo-400
                              hover:file:bg-indigo-600/20
                              file:cursor-pointer cursor-pointer
                              border border-slate-800 bg-slate-950 rounded-xl p-1.5">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-semibold text-sm transition-all shadow-md shadow-orange-600/10 hover:shadow-orange-500/20 flex items-center justify-center gap-2"
                    @click.prevent="if(document.querySelector('input[name=backup_file]').files.length) { confirmAction = 'upload-restore-form'; confirmTitle = 'Restore Database?'; confirmText = 'WARNING: Restoring the database will overwrite all existing tables, structures, and data. Are you sure you want to proceed?'; confirmBtnText = 'Restore'; isDelete = false; showConfirmModal = true; } else { document.getElementById('upload-restore-form').reportValidity(); }">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Upload & Restore
            </button>
        </form>
    </div>
    @endif

    <!-- Backup List -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'Backup Filename'],
        ['label' => 'Generation Date'],
        ['label' => 'File Size'],
        ['label' => 'Actions', 'align' => 'right']
    ];
    @endphp

    <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-800/60 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-400"></i>
                Backup Archives History
            </h3>
        </div>

        <x-admin.table :headers="$tableHeaders" :collection="$backups" type="flat" minWidth="600px">
            @forelse($backups as $index => $backup)
                <tr class="hover:bg-slate-800/20 transition-all text-sm text-slate-300">
                    <!-- No -->
                    <td class="px-6 py-4 font-mono text-xs text-slate-500">
                        #{{ $index + 1 }}
                    </td>
                    <!-- Filename -->
                    <td class="px-6 py-4 font-bold text-indigo-400 flex items-center gap-2">
                        <i class="fa-regular fa-file-archive text-slate-500"></i>
                        {{ $backup['name'] }}
                    </td>
                    <!-- Date -->
                    <td class="px-6 py-4 text-slate-400">
                        {{ $backup['created_at'] }}
                    </td>
                    <!-- Size -->
                    <td class="px-6 py-4 font-mono text-xs text-slate-300 font-bold">
                        {{ $backup['size'] }}
                    </td>
                    <!-- Actions -->
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="inline-flex gap-2">
                            <!-- Download -->
                            <a href="{{ route('admin.backups.download', $backup['name']) }}" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-300 hover:text-emerald-400 hover:border-emerald-500/50 transition-all flex items-center justify-center" title="Download Backup">
                                <i class="fa-solid fa-file-arrow-down text-sm"></i>
                            </a>
                            <!-- Restore -->
                            @if($canRestore)
                            <form id="restore-form-{{ $index }}" method="POST" action="{{ route('admin.backups.restore', $backup['name']) }}" class="inline">
                                @csrf
                                <button type="button" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-400 hover:text-orange-400 hover:border-orange-500/50 transition-all flex items-center justify-center cursor-pointer" title="Restore Database to this Backup"
                                        @click="confirmAction = 'restore-form-{{ $index }}'; confirmTitle = 'Restore Database?'; confirmText = 'WARNING: Restoring the database will overwrite all existing tables, structures, and data. Are you sure you want to restore the database to this backup?'; confirmBtnText = 'Restore'; isDelete = false; showConfirmModal = true;">
                                    <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                                </button>
                            </form>
                            @endif
                            <!-- Delete -->
                            <form id="delete-form-{{ $index }}" method="POST" action="{{ route('admin.backups.destroy', $backup['name']) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-400 hover:text-rose-400 hover:border-rose-500/50 transition-all cursor-pointer" title="Delete Backup"
                                        @click="confirmAction = 'delete-form-{{ $index }}'; confirmTitle = 'Delete Backup File?'; confirmText = 'Do you really want to permanently delete this backup file?'; confirmBtnText = 'Delete'; isDelete = true; showConfirmModal = true;">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 font-medium">No database backups found. Click 'Generate Backup' above to create one.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
    <!-- Confirm Modal -->
    <template x-teleport="body">
    <div x-show="showConfirmModal" 
         x-cloak 
         class="admin-modal fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="showConfirmModal = false"></div>

        <div x-show="showConfirmModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-sm rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 shadow-2xl space-y-6">
            <div class="text-center space-y-3">
                <div class="inline-flex p-3 rounded-2xl mb-2" :class="isDelete ? 'bg-rose-500/10 text-rose-500' : 'bg-orange-500/10 text-orange-500'">
                    <i class="fa-solid text-2xl animate-bounce" :class="isDelete ? 'fa-trash-can' : 'fa-triangle-exclamation'"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200" x-text="confirmTitle">Confirm Action</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400" x-text="confirmText"></p>
            </div>

            <div class="flex gap-4 pt-2">
                <button type="button" @click="showConfirmModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-650 dark:text-slate-300 font-semibold text-sm transition-all cursor-pointer">Cancel</button>
                <button type="button" @click="document.getElementById(confirmAction).submit(); showConfirmModal = false;" 
                        class="flex-1 py-3 px-4 rounded-xl font-bold text-sm shadow-xl transition-all cursor-pointer text-white"
                        :class="isDelete ? 'bg-gradient-to-r from-rose-500 to-red-600 shadow-rose-500/10 hover:shadow-rose-500/25' : 'bg-gradient-to-r from-orange-500 to-amber-600 shadow-orange-500/10 hover:shadow-orange-500/25'">
                    <span x-text="confirmBtnText">Confirm</span>
                </button>
            </div>
        </div>
    </div>
    </template>
</div>
@endsection
