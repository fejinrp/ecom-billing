@extends('layouts.admin', ['title' => 'Staff Attendance Management'])

@section('content')
<div x-data="{
    showSettingsModal: false,
    showAddModal: false,
    showEditModal: false,

    // Settings form states
    dailyWorkDuration: '{{ $settings['daily_work_duration'] ?? 8 }}',
    officeStartTime: '{{ $settings['office_start_time'] ?? '10:00' }}',
    officeEndTime: '{{ $settings['office_end_time'] ?? '18:00' }}',

    // Add form states
    addUserId: '',
    addDate: '',
    addPunchIn: '',
    addPunchOut: '',
    addNotes: '',
    addLat: '',
    addLong: '',
    geoStatus: 'Fetching location...',
    geoLocked: false,

    // Edit form states
    editId: '',
    editDate: '',
    editPunchIn: '',
    editPunchOut: '',
    editNotes: '',

    initGeo() {
        this.geoStatus = 'Detecting Location...';
        this.geoLocked = false;
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition((pos) => {
                this.addLat = pos.coords.latitude;
                this.addLong = pos.coords.longitude;
                this.geoLocked = true;
                this.geoStatus = 'Location Locked: ' + pos.coords.latitude.toFixed(4) + ', ' + pos.coords.longitude.toFixed(4);
            }, (err) => {
                this.geoStatus = 'Error: ' + err.message + '. Location is required.';
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        } else {
            this.geoStatus = 'Geolocation is not supported.';
        }
    },

    openAddModal() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        this.addDate = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
        this.addPunchIn = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
        this.addPunchOut = '';
        this.addNotes = '';
        this.addUserId = '';
        this.showAddModal = true;
        this.initGeo();
    },

    openEditModal(rec) {
        this.editId = rec.id;
        this.editDate = rec.date;
        this.editPunchIn = rec.punch_in ? rec.punch_in.substring(11, 16) : '';
        this.editPunchOut = rec.punch_out ? rec.punch_out.substring(11, 16) : '';
        this.editNotes = rec.notes ?? '';
        this.showEditModal = true;
    },

    calculateDuration() {
        if (this.officeStartTime && this.officeEndTime) {
            const s = new Date('1970-01-01T' + this.officeStartTime);
            const e = new Date('1970-01-01T' + this.officeEndTime);
            let diff = (e - s) / 1000 / 60 / 60;
            if (diff < 0) diff += 24;
            this.dailyWorkDuration = Math.round(diff * 100) / 100;
        }
    }
}" class="space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header title="Staff Attendance Log" description="Monitor employee shift schedules, punch-in/out logs, geolocation telemetry, and calculated payroll earnings.">
        <x-slot:action>
            <div class="flex items-center gap-2">
                <x-admin.button @click="showSettingsModal = true" variant="secondary" icon="fa-solid fa-cog">
                    Settings
                </x-admin.button>
                <x-admin.button @click="openAddModal()" icon="fa-solid fa-user-plus">
                    Manual Punch
                </x-admin.button>
            </div>
        </x-slot:action>
    </x-admin.header>

    <!-- Filters Section -->
    <div class="glassmorphism p-4 rounded-3xl flex flex-wrap gap-4 items-center justify-between">
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="flex items-center gap-3">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Select Log Date</label>
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" onchange="this.form.submit()"
                   class="px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
        </form>

        <div class="flex gap-4">
            <div class="flex items-center gap-2 bg-indigo-500/10 px-4 py-2 rounded-2xl border border-indigo-500/10">
                <i class="fa-solid fa-user-check text-indigo-400"></i>
                <div class="text-xs">
                    <span class="font-extrabold text-white block">{{ $attendances->count() }}</span>
                    <span class="text-slate-400 text-[10px] uppercase tracking-wider">Present Today</span>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-emerald-500/10 px-4 py-2 rounded-2xl border border-emerald-500/10">
                <i class="fa-solid fa-business-time text-emerald-400"></i>
                <div class="text-xs">
                    <span class="font-extrabold text-white block">{{ $attendances->where('punch_out', '!=', null)->count() }}</span>
                    <span class="text-slate-400 text-[10px] uppercase tracking-wider">Shift Completed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert for Missing Rates -->
    @php
        $staffWithNoRate = $attendances->filter(fn($r) => !$r->user || !$r->user->hourly_rate || $r->user->hourly_rate == 0)->count();
    @endphp
    @if($staffWithNoRate > 0)
        <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex gap-3 text-amber-300">
            <i class="fa-solid fa-triangle-exclamation text-lg mt-0.5"></i>
            <div class="text-xs space-y-1">
                <strong class="font-black uppercase tracking-wider">Hourly Rates Missing!</strong>
                <p class="text-slate-400 font-medium">There are {{ $staffWithNoRate }} employee check-ins missing an hourly rate. Add their rate in Users Admin to calculate payouts.</p>
            </div>
        </div>
    @endif

    <!-- Table Config -->
    @php
    $tableHeaders = [
        ['label' => 'Staff Member'],
        ['label' => 'Punch In'],
        ['label' => 'Punch Out'],
        ['label' => 'Status'],
        ['label' => 'Duration'],
        ['label' => 'Earned Payout', 'align' => 'right'],
        ['label' => 'Notes'],
        ['label' => 'Options', 'align' => 'right']
    ];
    @endphp

    <x-admin.table :headers="$tableHeaders" :collection="$attendances" type="card" minWidth="800px">
        @forelse($attendances as $index => $rec)
            @php
                $hasRate = $rec->user && (float)$rec->user->hourly_rate > 0;
                $statusColor = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                if($rec->status == 'Late') $statusColor = 'bg-rose-500/10 text-rose-450 border border-rose-500/20';
                if($rec->status == 'Early Leave') $statusColor = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
            @endphp
            <tr class="hover:bg-slate-800/30 transition-all block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-4 lg:p-0 mb-4 lg:mb-0 relative grid grid-cols-2 gap-x-4 gap-y-3 lg:grid-cols-none lg:bg-transparent lg:border-0 lg:hover:bg-slate-900/20">
                
                <!-- Staff -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Staff Member</span>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-sm uppercase">
                            {{ substr($rec->user->username, 0, 2) }}
                        </div>
                        <div>
                            <span class="font-bold text-slate-200 uppercase block text-sm">{{ $rec->user->username }}</span>
                            <span class="text-slate-400 text-[10px] font-mono block">{{ $rec->user->mobile }}</span>
                        </div>
                    </div>
                </td>

                <!-- Punch In -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Punch In</span>
                    <span class="font-bold text-slate-200 block text-xs font-mono">{{ $rec->punch_in ? date('h:i A', strtotime($rec->punch_in)) : 'N/A' }}</span>
                    @if($rec->late_minutes > 0)
                        <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-rose-500/10 text-rose-450 mt-1">{{ $rec->late_minutes }}m Late</span>
                    @endif
                </td>

                <!-- Punch Out -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Punch Out</span>
                    @if($rec->punch_out)
                        <span class="font-bold text-slate-200 block text-xs font-mono">{{ date('h:i A', strtotime($rec->punch_out)) }}</span>
                        @if($rec->early_exit_minutes > 0)
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-amber-500/10 text-amber-450 mt-1">{{ $rec->early_exit_minutes }}m Early</span>
                        @endif
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20">Active Shift</span>
                    @endif
                </td>

                <!-- Status -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</span>
                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $statusColor }}">{{ $rec->status }}</span>
                </td>

                <!-- Duration -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Duration</span>
                    <span class="text-slate-300 font-semibold text-xs font-mono">{{ $rec->total_hours ?? 'Ongoing...' }}</span>
                </td>

                <!-- Payout -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none lg:text-right">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Earned Payout</span>
                    @if($rec->total_hours)
                        <span class="text-emerald-450 font-bold block text-sm font-mono">₹{{ number_format($rec->earned_salary, 2) }}</span>
                        <span class="text-slate-500 text-[10px] block font-medium">₹{{ floor($rec->user->hourly_rate) }}/hr</span>
                    @else
                        <span class="text-slate-500 text-xs">-</span>
                    @endif
                </td>

                <!-- Notes -->
                <td class="py-2 lg:px-6 lg:py-4 col-span-1 block lg:table-cell lg:col-span-none">
                    <span class="block lg:hidden text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Notes</span>
                    <span class="text-slate-400 text-xs block truncate max-w-[120px]" title="{{ $rec->notes }}">{{ $rec->notes ?? '---' }}</span>
                </td>

                <!-- Options -->
                <td class="col-span-2 py-2 lg:px-6 lg:py-4 lg:text-right block lg:table-cell lg:col-span-none border-t border-slate-800/40 lg:border-0 pt-3 lg:pt-0">
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="openEditModal({{ $rec }})" class="p-2 hover:bg-slate-800/80 text-indigo-400 rounded-xl transition border border-transparent hover:border-slate-700/50" title="Edit Log">
                            <i class="fa-regular fa-edit text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row w-full bg-slate-900/30 border border-slate-800/60 rounded-2xl p-6 text-center">
                <td colspan="8" class="text-slate-500 py-8 font-semibold">No attendance check-ins recorded for this date.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <!-- 1. Settings Modal -->
    <x-admin.modal id="showSettingsModal" title="Office Configuration" icon="fa-solid fa-cog">
        <form action="{{ route('admin.attendance.updateSettings') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Shift Start *</label>
                    <input type="time" name="office_start_time" x-model="officeStartTime" @change="calculateDuration()" required 
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Shift Close *</label>
                    <input type="time" name="office_end_time" x-model="officeEndTime" @change="calculateDuration()" required 
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Standard Daily Shift Hours *</label>
                <input type="number" name="daily_work_duration" x-model="dailyWorkDuration" step="0.1" required 
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                <small class="text-[10px] text-slate-400 block mt-1">Standard shift length before overtime calculates automatically.</small>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="button" @click="showSettingsModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-indigo-650/20">Save Settings</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 2. Manual Punch Entry Modal -->
    <x-admin.modal id="showAddModal" title="Manual Check-In Log" icon="fa-solid fa-user-plus">
        <form action="{{ route('admin.attendance.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Select Staff Member *</label>
                <select name="user_id" x-model="addUserId" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                    <option value="">-- CHOOSE USER --</option>
                    @foreach($staffList as $staff)
                        <option value="{{ $staff->user_id }}">{{ $staff->username }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Log Date *</label>
                <input type="date" name="date" x-model="addDate" required 
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Punch In *</label>
                    <input type="time" name="punch_in" x-model="addPunchIn" required 
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Punch Out</label>
                    <input type="time" name="punch_out" x-model="addPunchOut" 
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Remarks / Notes</label>
                <textarea name="notes" x-model="addNotes" placeholder="REASON FOR MANUAL CHECK-IN CORRECTION..." rows="2"
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase"></textarea>
            </div>

            <div class="p-3 bg-slate-950 border border-slate-850 rounded-2xl flex items-center justify-between gap-4">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider" x-text="geoStatus"></div>
                <input type="hidden" name="latitude" x-model="addLat">
                <input type="hidden" name="longitude" x-model="addLong">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition">Cancel</button>
                <button type="submit" :disabled="!geoLocked" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-indigo-650/20 disabled:opacity-50 disabled:cursor-not-allowed">Save Record</button>
            </div>
        </form>
    </x-admin.modal>

    <!-- 3. Edit Punch Log Modal -->
    <x-admin.modal id="showEditModal" title="Modify Punch Log" icon="fa-solid fa-edit">
        <form :action="`/admin/attendance/${editId}`" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Log Date *</label>
                <input type="date" name="date" x-model="editDate" required 
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Punch In *</label>
                    <input type="time" name="punch_in" x-model="editPunchIn" required 
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Punch Out</label>
                    <input type="time" name="punch_out" x-model="editPunchOut" 
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500 text-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Remarks / Notes</label>
                <textarea name="notes" x-model="editNotes" placeholder="REASON FOR MANUAL CHECK-IN CORRECTION..." rows="2"
                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500 text-sm uppercase"></textarea>
            </div>

            <div class="p-3 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-2xl text-[10px] font-bold uppercase tracking-wider">
                Note: Updating shift check times will trigger immediate recalculation of total hours and payroll earnings.
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-indigo-650/20">Update Record</button>
            </div>
        </form>
    </x-admin.modal>

</div>
@endsection
