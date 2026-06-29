@extends('layouts.admin', ['title' => 'My Attendance'])

@section('content')
<div x-data="{
    liveTime: '00:00:00',
    liveDate: '{{ date('l, d M Y') }}',
    punchNotes: '',
    isWorking: {{ $attendance && !$attendance->punch_out ? 'true' : 'false' }},
    isFinished: {{ $attendance && $attendance->punch_out ? 'true' : 'false' }},

    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
    },

    updateClock() {
        const now = new Date();
        this.liveTime = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    },

    handlePunch(type) {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Geolocation is not supported by your browser.', 'error');
            return;
        }

        Swal.fire({
            title: 'Fetching Location...',
            text: 'Verifying your physical coordinate profile...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        navigator.geolocation.getCurrentPosition((pos) => {
            const lat = pos.coords.latitude;
            const long = pos.coords.longitude;

            fetch(`/admin/my-attendance/punch-${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    lat: lat,
                    long: long,
                    notes: this.punchNotes
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Punch Successful',
                        text: res.message,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Validation Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Server Error', 'Could not establish connection to server.', 'error');
            });
        }, (error) => {
            let msg = 'Unknown location error';
            if (error.code === error.PERMISSION_DENIED) msg = 'Location permission denied. Please enable GPS permissions.';
            if (error.code === error.POSITION_UNAVAILABLE) msg = 'GPS signals unavailable.';
            if (error.code === error.TIMEOUT) msg = 'Location detection timed out.';
            Swal.fire('Location Error', msg, 'error');
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
    }
}" class="max-w-4xl mx-auto space-y-8 animate-fadeIn">

    <!-- Header Section -->
    <x-admin.header title="My Punch Card" description="Check-in or checkout of your shift session. Coordinates, IP, and device telemetry are validated on check-in."></x-admin.header>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
        
        <!-- Left Column: Clock & Trigger -->
        <div class="md:col-span-2 bg-white dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800/80 shadow-md dark:shadow-none backdrop-blur-md p-8 rounded-3xl text-center flex flex-col justify-between items-center space-y-6">
            
            <div class="space-y-2 w-full">
                <!-- Clock face -->
                <div x-text="liveTime" class="text-4xl font-extrabold text-slate-800 dark:text-white tracking-widest font-mono bg-slate-100 dark:bg-slate-950/60 py-4 px-6 rounded-2xl border border-slate-200 dark:border-slate-800"></div>
                <div x-text="liveDate" class="text-xs font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-wider"></div>
            </div>

            @if($staff && $staff->shift_start && $staff->shift_end)
                <div class="inline-flex px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 border border-indigo-550/10 dark:border-indigo-500/20 gap-1.5 items-center">
                    <i class="fa-regular fa-clock"></i>
                    <span>Shift: {{ date('h:i A', strtotime($staff->shift_start)) }} - {{ date('h:i A', strtotime($staff->shift_end)) }}</span>
                </div>
            @endif

            <!-- Punch Button Layout -->
            <div class="relative w-44 h-44 flex items-center justify-center">
                <template x-if="!isWorking && !isFinished">
                    <button @click="handlePunch('in')" class="w-40 h-40 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-400 text-white font-extrabold flex flex-col items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all duration-300 border-8 border-slate-100 dark:border-slate-900/60">
                        <i class="fa-solid fa-right-to-bracket text-3xl"></i>
                        <span class="text-xs uppercase tracking-widest">Punch In</span>
                    </button>
                </template>
                <template x-if="isWorking && !isFinished">
                    <button @click="handlePunch('out')" class="w-40 h-40 rounded-full bg-gradient-to-tr from-rose-600 to-orange-400 text-white font-extrabold flex flex-col items-center justify-center gap-2 shadow-lg shadow-rose-500/20 hover:scale-105 active:scale-95 transition-all duration-300 border-8 border-slate-100 dark:border-slate-900/60">
                        <i class="fa-solid fa-right-from-bracket text-3xl"></i>
                        <span class="text-xs uppercase tracking-widest">Punch Out</span>
                    </button>
                </template>
                <template x-if="isFinished">
                    <div class="w-40 h-40 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-500 font-extrabold flex flex-col items-center justify-center gap-2 border-8 border-slate-100 dark:border-slate-900/60">
                        <i class="fa-solid fa-circle-check text-3xl text-slate-400 dark:text-slate-600"></i>
                        <span class="text-xs uppercase tracking-widest">Completed</span>
                    </div>
                </template>
            </div>

            <!-- Notes field -->
            <div class="w-full text-left space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Session Remarks (Optional)</label>
                <textarea x-model="punchNotes" placeholder="REASON FOR LATE ENTRY, DEVIATION, OR REMARKS..." rows="2"
                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-indigo-500 text-xs uppercase"></textarea>
            </div>

        </div>

        <!-- Right Column: Status & History -->
        <div class="md:col-span-3 space-y-6">
            
            <!-- Quick metrics panel -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl text-center shadow-sm dark:shadow-none">
                    <span class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Check In</span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $attendance && $attendance->punch_in ? date('h:i A', strtotime($attendance->punch_in)) : '--:--' }}</span>
                </div>
                <div class="bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl text-center shadow-sm dark:shadow-none">
                    <span class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Check Out</span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $attendance && $attendance->punch_out ? date('h:i A', strtotime($attendance->punch_out)) : '--:--' }}</span>
                </div>
                <div class="bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl text-center shadow-sm dark:shadow-none">
                    <span class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Shift Earned</span>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">{{ $attendance && $attendance->earned_salary ? '₹'.number_format($attendance->earned_salary, 2) : '₹0.00' }}</span>
                </div>
            </div>

            <!-- History log -->
            <div class="bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl space-y-4 shadow-sm dark:shadow-none">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-500 dark:text-indigo-400"></i>
                        Recent Check-Ins (10 days)
                    </h4>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Month Total: <span class="text-indigo-500 dark:text-indigo-400">₹{{ number_format($monthlyEarned, 2) }}</span></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[9px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-250 dark:border-slate-800/40">
                                <th class="pb-2">Date</th>
                                <th class="pb-2">Check In</th>
                                <th class="pb-2">Check Out</th>
                                <th class="pb-2">Hours</th>
                                <th class="pb-2 text-right">Earned</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $rec)
                                <tr class="border-b border-slate-100 dark:border-slate-800/20 text-xs hover:bg-slate-100/50 dark:hover:bg-slate-800/10 transition-all">
                                    <td class="py-2.5 font-bold text-slate-700 dark:text-slate-300 font-mono">{{ date('d M', strtotime($rec->date)) }}</td>
                                    <td class="py-2.5 text-slate-600 dark:text-slate-400 font-mono">{{ date('h:i A', strtotime($rec->punch_in)) }}</td>
                                    <td class="py-2.5 text-slate-600 dark:text-slate-400 font-mono">{{ $rec->punch_out ? date('h:i A', strtotime($rec->punch_out)) : '---' }}</td>
                                    <td class="py-2.5">
                                        <span class="text-slate-650 dark:text-slate-400 font-semibold font-mono text-[11px]">{{ $rec->total_hours ?? 'Ongoing' }}</span>
                                    </td>
                                    <td class="py-2.5 text-right font-bold text-emerald-600 dark:text-emerald-450 font-mono">₹{{ number_format($rec->earned_salary, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-slate-500 font-medium">No check-in history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
