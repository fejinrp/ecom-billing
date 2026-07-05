@extends('layouts.admin')

@section('content')
<div x-data="{ 
    showAddModal: {{ session('reopen_modal') === 'add' ? 'true' : 'false' }}, 
    showEditModal: {{ session('reopen_modal') === 'edit' ? 'true' : 'false' }}, 
    showStatusModal: false,
    
    // Add form states
    addUname: '',
    addEmail: '',
    addContactno: '',
    addUsertype: '',
    addPassword: '',
    
    // Edit form states
    editUserId: '',
    editUname: '',
    editEmail: '',
    editContactno: '',
    editUsertype: '',
    editPassword: '',
    
    // Status states
    statusUserId: '',
    statusUserAction: '',
    statusMessage: '',
    
    openEditModal(user) {
        this.editUserId = user.id;
        this.editUname = user.uname;
        this.editEmail = user.email;
        this.editContactno = user.contactno;
        this.editUsertype = user.usertype;
        this.editPassword = '';
        this.showEditModal = true;
    },
    
    openStatusModal(user) {
        this.statusUserId = user.id;
        this.statusUserAction = user.ustatus == 1 ? 'Deactivate' : 'Activate';
        this.statusMessage = user.ustatus == 1 ? 'Do you really want to deactivate this customer/dealer account?' : 'Do you really want to activate this customer/dealer account?';
        this.showStatusModal = true;
    }
}" class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header title="Manage Online Users" description="Configure online storefront customers, dealers, and super dealer portal access profiles. (ref: admin/usercustomer.php)" glass="false">
        <x-slot:action>
            <x-admin.button @click="showAddModal = true" icon="fa-solid fa-user-tag">
                Add Customer
            </x-admin.button>
        </x-slot:action>
    </x-admin.header>

    <!-- Search & Filter Bar -->
    <x-admin.search-bar :action="route('admin.customers.index')" placeholder="Search by name, email or mobile number...">
        <x-slot:info>
            <span>Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} customer records</span>
        </x-slot:info>
    </x-admin.search-bar>

    <!-- Table Config -->
    @php
    $tableHeaders = [
        ['label' => 'No'],
        ['label' => 'Cust Name'],
        ['label' => 'Email ID'],
        ['label' => 'Mobile No'],
        ['label' => 'User Type'],
        ['label' => 'Status'],
        ['label' => 'Options', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$users" type="card" minWidth="600px">
        @forelse($users as $index => $user)
            <tr class="hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20">
                
                <!-- No -->
                <td class="col-span-2 bg-indigo-500/10 text-indigo-400 px-3 py-2 rounded-lg text-xs font-mono font-bold flex items-center justify-between lg:table-cell lg:col-span-none lg:bg-transparent lg:px-6 lg:py-4 lg:text-slate-400">
                    <span class="lg:hidden uppercase tracking-wider text-[10px] font-bold text-indigo-400">No</span>
                    <span>#{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</span>
                </td>

                <!-- Customer Name -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cust Name</span>
                    <span class="font-bold text-indigo-400 flex items-center gap-1.5">
                        <i class="fa-regular fa-user text-slate-500"></i>
                        {{ $user->uname }}
                    </span>
                </td>

                <!-- Email -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Email ID</span>
                    <span class="text-slate-300 font-medium">{{ $user->email }}</span>
                </td>

                <!-- Mobile -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Mobile No</span>
                    <span class="text-slate-400 font-mono text-xs">{{ $user->contactno }}</span>
                </td>

                <!-- User Type -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">User Type</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $user->usertype == 'S' ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : ($user->usertype == 'D' ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' : 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20') }}">
                        {{ $user->usertype == 'S' ? 'Super Dealer' : ($user->usertype == 'D' ? 'Dealer' : 'Customer') }}
                    </span>
                </td>

                <!-- Status -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $user->ustatus == 1 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                        {{ $user->ustatus == 1 ? 'Active' : 'Inactive' }}
                    </span>
                </td>

                <!-- Options -->
                <td class="py-3 border-t border-slate-800/40 mt-2 lg:border-t-0 lg:mt-0 col-span-2 block lg:table-cell lg:col-span-none lg:px-6 lg:py-4 lg:text-right whitespace-nowrap">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Options</span>
                    <div class="inline-flex gap-2">
                        <button @click="openEditModal({{ json_encode($user) }})" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-300 hover:text-indigo-400 hover:border-indigo-500/50 transition-all" title="Edit Customer">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <button @click="openStatusModal({{ json_encode($user) }})" class="p-2.5 rounded-xl bg-slate-800 border border-slate-700/50 text-slate-400 hover:text-emerald-400 hover:border-emerald-500/50 transition-all" title="Toggle Status">
                            <i class="fa-solid fa-power-off text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 lg:p-0">
                <td colspan="7" class="px-6 py-8 text-center text-slate-500 font-medium block lg:table-cell w-full">
                    @if(request('search'))
                        No customer or dealer accounts found matching <span class="text-indigo-400 font-semibold">"{{ request('search') }}"</span>.
                    @else
                        No customer or dealer accounts found. Click 'Add Customer' to create one.
                    @endif
                </td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Add Customer Modal -->
    <x-admin.modal id="showAddModal" title="Add Customer" icon="fa-solid fa-user-plus">
        <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-4">
            @csrf

            <!-- Customer Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Customer Name</label>
                <input type="text" 
                       name="uname" 
                       required 
                       value="{{ old('uname') }}"
                       placeholder="Enter customer name"
                       class="w-full bg-slate-950 border {{ $errors->has('uname') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('uname')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email ID</label>
                <input type="email" 
                       name="email" 
                       required 
                       value="{{ old('email') }}"
                       placeholder="Enter email ID"
                       class="w-full bg-slate-950 border {{ $errors->has('email') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Contact No -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Mobile No</label>
                <input type="text" 
                       name="contactno" 
                       required 
                       value="{{ old('contactno') }}"
                       placeholder="Enter mobile number"
                       maxlength="10"
                       class="w-full bg-slate-950 border {{ $errors->has('contactno') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('contactno')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- User Type -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">User Type</label>
                <select name="usertype" required class="w-full bg-slate-950 border {{ $errors->has('usertype') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~~Select Type~~</option>
                    <option value="C" {{ old('usertype') == 'C' ? 'selected' : '' }}>Customer</option>
                    <option value="D" {{ old('usertype') == 'D' ? 'selected' : '' }}>Dealer</option>
                    <option value="S" {{ old('usertype') == 'S' ? 'selected' : '' }}>Super Dealer</option>
                </select>
                @error('usertype')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                <input type="password" 
                       name="password" 
                       required 
                       placeholder="Enter password"
                       class="w-full bg-slate-950 border {{ $errors->has('password') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showAddModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all">Save Changes</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Edit Customer Modal -->
    <x-admin.modal id="showEditModal" title="Edit Customer" icon="fa-solid fa-pen-to-square">
        <form method="POST" :action="'{{ route('admin.customers.index') }}/' + editUserId" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Customer Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Customer Name</label>
                <input type="text" 
                       name="uname" 
                       required 
                       x-model="editUname"
                       placeholder="Enter customer name"
                       class="w-full bg-slate-950 border {{ $errors->has('uname') && session('reopen_modal') === 'edit' ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @if($errors->has('uname') && session('reopen_modal') === 'edit')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('uname') }}
                    </p>
                @endif
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email ID</label>
                <input type="email" 
                       name="email" 
                       required 
                       x-model="editEmail"
                       placeholder="Enter email ID"
                       class="w-full bg-slate-950 border {{ $errors->has('email') && session('reopen_modal') === 'edit' ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @if($errors->has('email') && session('reopen_modal') === 'edit')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('email') }}
                    </p>
                @endif
            </div>

            <!-- Contact No -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Mobile No</label>
                <input type="text" 
                       name="contactno" 
                       required 
                       x-model="editContactno"
                       placeholder="Enter mobile no"
                       maxlength="10"
                       class="w-full bg-slate-950 border {{ $errors->has('contactno') && session('reopen_modal') === 'edit' ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @if($errors->has('contactno') && session('reopen_modal') === 'edit')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('contactno') }}
                    </p>
                @endif
            </div>

            <!-- User Type -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">User Type</label>
                <select name="usertype" required x-model="editUsertype" class="w-full bg-slate-950 border {{ $errors->has('usertype') && session('reopen_modal') === 'edit' ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">~~Select Type~~</option>
                    <option value="C">Customer</option>
                    <option value="D">Dealer</option>
                    <option value="S">Super Dealer</option>
                </select>
                @if($errors->has('usertype') && session('reopen_modal') === 'edit')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('usertype') }}
                    </p>
                @endif
            </div>

            <!-- Password (Optional) -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password <span class="text-[10px] text-slate-500 normal-case">(Leave blank to keep current)</span></label>
                <input type="password" 
                       name="password" 
                       placeholder="Enter new password"
                       class="w-full bg-slate-950 border {{ $errors->has('password') && session('reopen_modal') === 'edit' ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-3 text-sm text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @if($errors->has('password') && session('reopen_modal') === 'edit')
                    <p class="mt-1.5 text-xs text-rose-400 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('password') }}
                    </p>
                @endif
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-850">
                <button type="button" @click="showEditModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-xl shadow-orange-600/10 hover:shadow-orange-600/25 transition-all">Save Changes</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Toggle Status Modal -->
    <x-admin.modal id="showStatusModal" title="Toggle Account Status" maxWidth="sm">
        <div class="text-center space-y-3">
            <div class="inline-flex p-3 rounded-2xl bg-indigo-500/10 text-indigo-400 mb-2">
                <i class="fa-solid fa-power-off text-2xl animate-pulse"></i>
            </div>
            <p class="text-sm text-slate-400" x-text="statusMessage"></p>
        </div>

        <form method="POST" :action="'{{ route('admin.customers.index') }}/' + statusUserId + '/toggle-status'">
            @csrf
            
            <div class="flex gap-4 pt-2">
                <button type="button" @click="showStatusModal = false" class="flex-1 py-3 px-4 rounded-xl border border-slate-800 hover:bg-slate-800/50 text-slate-300 font-semibold text-sm transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold text-sm shadow-xl shadow-indigo-500/10 hover:shadow-indigo-500/25 transition-all" x-text="statusUserAction"></button>
            </div>
        </form>
    </x-admin.modal>

</div>
@endsection
