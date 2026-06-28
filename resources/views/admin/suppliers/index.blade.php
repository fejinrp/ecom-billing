@extends('layouts.admin', ['title' => 'Manage Suppliers'])

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showStatusModal: false,
    
    // Add form states
    addName: '',
    addContactPerson: '',
    addPhone: '',
    addEmail: '',
    addAddress: '',
    
    // Edit form states
    editSupplierId: '',
    editName: '',
    editContactPerson: '',
    editPhone: '',
    editEmail: '',
    editAddress: '',
    
    // Status states
    statusSupplierId: '',
    statusSupplierAction: '',
    statusMessage: '',
    
    openEditModal(supplier) {
        this.editSupplierId = supplier.id;
        this.editName = supplier.name;
        this.editContactPerson = supplier.contact_person ?? '';
        this.editPhone = supplier.phone ?? '';
        this.editEmail = supplier.email ?? '';
        this.editAddress = supplier.address ?? '';
        this.showEditModal = true;
    },
    
    openStatusModal(supplier) {
        this.statusSupplierId = supplier.id;
        this.statusSupplierAction = supplier.status == 1 ? 'Deactivate' : 'Activate';
        this.statusMessage = supplier.status == 1 ? 'Do you really want to deactivate this supplier account?' : 'Do you really want to activate this supplier account?';
        this.showStatusModal = true;
    }
}" class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header title="Manage Suppliers" description="Configure master supplier records, contacts, addresses, and track outstanding ledger balances." glass="false">
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-truck-ramp-box">
                Add Supplier
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Table Config -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'Supplier Name'],
        ['label' => 'Contact Person'],
        ['label' => 'Phone'],
        ['label' => 'Email'],
        ['label' => 'Address'],
        ['label' => 'Status'],
        ['label' => 'Options', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$suppliers" type="card" minWidth="600px">
        @forelse($suppliers as $index => $supplier)
            <tr class="hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20">
                
                <!-- No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">No</span>
                    <span>#{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $index + 1 }}</span>
                </td>

                <!-- Supplier Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Supplier Name</span>
                    <span class="font-bold text-indigo-400 flex items-center gap-1.5 uppercase">
                        <i class="fa-solid fa-truck-moving text-slate-500 text-xs"></i>
                        {{ $supplier->name }}
                    </span>
                </td>

                <!-- Contact Person -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Contact Person</span>
                    <span class="text-slate-300 font-medium uppercase">{{ $supplier->contact_person ?? 'N/A' }}</span>
                </td>

                <!-- Phone -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Phone</span>
                    <span class="text-slate-400 font-mono text-xs">{{ $supplier->phone ?? 'N/A' }}</span>
                </td>

                <!-- Email -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Email</span>
                    <span class="text-slate-400 text-xs">{{ $supplier->email ?? 'N/A' }}</span>
                </td>

                <!-- Address -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Address</span>
                    <span class="text-slate-400 text-xs block truncate max-w-[150px] uppercase" title="{{ $supplier->address }}">{{ $supplier->address ?? 'N/A' }}</span>
                </td>

                <!-- Status -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</span>
                    @if($supplier->status == 1)
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-500/10 text-emerald-450 border border-emerald-500/20">Active</span>
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-rose-500/10 text-rose-450 border border-rose-500/20">Inactive</span>
                    @endif
                </td>

                <!-- Options -->
                <td class="col-span-2 py-2 lg:px-6 lg:py-4 lg:text-right block lg:table-cell lg:col-span-none border-t border-slate-800/40 lg:border-0 pt-3 lg:pt-0">
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="openEditModal({{ $supplier }})" class="p-2 hover:bg-slate-800/80 text-indigo-400 rounded-xl transition border border-transparent hover:border-slate-700/50" title="Edit Profile">
                            <i class="fa-regular fa-edit text-sm"></i>
                        </button>
                        <button type="button" @click="openStatusModal({{ $supplier }})" 
                                class="p-2 hover:bg-slate-800/80 rounded-xl transition border border-transparent hover:border-slate-700/50 {{ $supplier->status == 1 ? 'text-rose-400' : 'text-emerald-400' }}" 
                                title="{{ $supplier->status == 1 ? 'Deactivate Account' : 'Activate Account' }}">
                            <i class="fa-solid {{ $supplier->status == 1 ? 'fa-ban' : 'fa-check-circle' }} text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 text-center">
                <td colspan="8" class="text-slate-500 py-8 font-semibold">No suppliers configured yet.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Add Supplier Modal -->
    <x-admin.modal id="showAddModal" title="Create Supplier Record" icon="fa-solid fa-truck-ramp-box">
        <form action="{{ route('admin.suppliers.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Supplier Name *</label>
                <input type="text" name="name" x-model="addName" required placeholder="ENTER SUPPLIER / COMPANY NAME" 
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 uppercase">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Contact Person</label>
                <input type="text" name="contact_person" x-model="addContactPerson" placeholder="ENTER REPRESENTATIVE NAME" 
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 uppercase">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Phone / Mobile</label>
                    <input type="text" name="phone" x-model="addPhone" placeholder="ENTER PHONE NUMBER" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 font-mono">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" x-model="addEmail" placeholder="ENTER EMAIL ADDRESS" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Street Address / Details</label>
                <textarea name="address" x-model="addAddress" placeholder="ENTER STREET, CITY, STATE & GSTIN DETAILS..." rows="3"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 uppercase"></textarea>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 hover:shadow-indigo-600/25 transition-all">Save Supplier</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit Supplier Modal -->
    <x-admin.modal id="showEditModal" title="Modify Supplier Record" icon="fa-solid fa-edit">
        <form :action="`/admin/suppliers/${editSupplierId}`" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Supplier Name *</label>
                <input type="text" name="name" x-model="editName" required placeholder="ENTER SUPPLIER / COMPANY NAME" 
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 uppercase">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Contact Person</label>
                <input type="text" name="contact_person" x-model="editContactPerson" placeholder="ENTER REPRESENTATIVE NAME" 
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 uppercase">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Phone / Mobile</label>
                    <input type="text" name="phone" x-model="editPhone" placeholder="ENTER PHONE NUMBER" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 font-mono">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" x-model="editEmail" placeholder="ENTER EMAIL ADDRESS" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Street Address / Details</label>
                <textarea name="address" x-model="editAddress" placeholder="ENTER STREET, CITY, STATE & GSTIN DETAILS..." rows="3"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 uppercase"></textarea>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showEditModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/10 hover:shadow-indigo-600/25 transition-all">Update Supplier</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Status Toggle Modal -->
    <x-admin.modal id="showStatusModal" title="Toggle Account Status" maxWidth="sm">
        <form :action="`/admin/suppliers/${statusSupplierId}/toggle-status`" method="POST" class="space-y-6 text-center">
            @csrf
            
            <div class="w-16 h-16 bg-amber-500/10 border border-amber-500/25 rounded-full flex items-center justify-center mx-auto text-amber-500">
                <i class="fa-solid fa-triangle-exclamation text-2xl animate-pulse"></i>
            </div>
            
            <div class="space-y-2">
                <h4 class="text-sm font-black text-slate-200 uppercase tracking-wider">Confirm Status Adjustment</h4>
                <p class="text-xs text-slate-400 leading-relaxed" x-text="statusMessage"></p>
            </div>
            
            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showStatusModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">No, Abort</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow-xl shadow-amber-650/20 transition-all" x-text="`Yes, ${statusSupplierAction}`"></button>
            </div>
        </form>
    </x-admin.modal>

</div>
@endsection
