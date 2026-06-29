<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Auser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Admin View: All Attendance
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->has('date')) {
            $query->where('date', $request->date);
        } else {
            $query->where('date', date('Y-m-d'));
        }

        $attendances = $query->orderBy('id', 'desc')->get();
        // Section 2 users are standard staff/executives in the system
        $staffList = Auser::where('ustatus', 1)->orderBy('username', 'asc')->get();
        
        // Fetch settings
        $settings = DB::table('settings')->pluck('value', 'key');
        
        return view('admin.attendance.index', compact('attendances', 'staffList', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'daily_work_duration' => 'required|numeric|min:1|max:24',
            'office_start_time' => 'required',
            'office_end_time' => 'required',
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'daily_work_duration'],
            ['value' => $request->daily_work_duration, 'description' => 'Standard daily work hours']
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'office_start_time'],
            ['value' => $request->office_start_time, 'description' => 'Office start time']
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'office_end_time'],
            ['value' => $request->office_end_time, 'description' => 'Office end time']
        );

        return redirect()->back()->with('success', 'Office settings updated successfully.');
    }

    private function calculateAttendanceMetrics($staff, $date, $punchIn, $punchOut, $attendanceId = null) {
        $punchIn = Carbon::parse($punchIn);

        // Check if this is the first record of the day to apply 'Late' status
        $isFirstSession = !Attendance::where('user_id', $staff->user_id)
            ->where('date', $date)
            ->when($attendanceId, function($q) use ($attendanceId) {
                return $q->where('id', '!=', $attendanceId);
            })
            ->exists();

        // Get shift times with Global Settings fallback
        $officeStartStr = DB::table('settings')->where('key', 'office_start_time')->value('value') ?? '10:00';
        $officeEndStr = DB::table('settings')->where('key', 'office_end_time')->value('value') ?? '18:00';
        
        $shiftStartStr = $staff->shift_start ?: $officeStartStr;
        $shiftEndStr = $staff->shift_end ?: $officeEndStr;
        
        $shiftStart = Carbon::parse($date . ' ' . $shiftStartStr);
        $shiftEnd = Carbon::parse($date . ' ' . $shiftEndStr);
        
        // EFFECTIVE PUNCH OUT: Use provided punchOut or fallback to current time
        $effectivePunchOut = $punchOut ? Carbon::parse($punchOut) : Carbon::now();
        
        $metrics = [
            'total_hours' => null,
            'overtime_hours' => null,
            'overtime_minutes' => 0,
            'late_minutes' => 0,
            'early_exit_minutes' => 0,
            'earned_salary' => 0,
            'basic_earned' => 0,
            'overtime_earned' => 0,
            'status' => 'Present'
        ];

        // 1. Late Calculation - Only for absolute first session of the day
        if ($isFirstSession && $punchIn->gt($shiftStart)) {
            $metrics['late_minutes'] = $punchIn->diffInMinutes($shiftStart);
            $metrics['status'] = 'Late';
        }

        // 2. Duration Calculation
        if ($effectivePunchOut->gt($punchIn)) {
            $totalMinutes = $punchIn->diffInMinutes($effectivePunchOut);
        } else {
            $totalMinutes = 0;
        }

        // 3. Overtime Logic: Early Punch-In + Late Punch-Out
        $earlyOtMinutes = 0;
        if ($isFirstSession && $punchIn->lt($shiftStart)) {
            $earlyOtMinutes = $shiftStart->diffInMinutes($punchIn);
        }

        $lateOtMinutes = 0;
        if ($effectivePunchOut->gt($shiftEnd)) {
            $lateOtMinutes = $effectivePunchOut->diffInMinutes($shiftEnd);
        }

        $totalOtMinutes = $earlyOtMinutes + $lateOtMinutes;
        
        // Ensure OT doesn't exceed total work time
        if ($totalOtMinutes > $totalMinutes) $totalOtMinutes = $totalMinutes;

        $basicMinutes = $totalMinutes - $totalOtMinutes;
        
        // Financials: All hours calculate same hour value
        $hourlyRate = (float)($staff->hourly_rate ?? 0);
        $metrics['total_hours'] = floor($totalMinutes / 60) . " hours " . ($totalMinutes % 60) . " min";
        $metrics['overtime_minutes'] = $totalOtMinutes;
        $metrics['basic_earned'] = round(($basicMinutes / 60) * $hourlyRate, 2);
        $metrics['overtime_earned'] = round(($totalOtMinutes / 60) * $hourlyRate, 2);
        $metrics['earned_salary'] = round(($totalMinutes / 60) * $hourlyRate, 2);

        // 4. Early Exit
        if ($punchOut && $effectivePunchOut->lt($shiftEnd)) {
            $metrics['early_exit_minutes'] = $effectivePunchOut->diffInMinutes($shiftEnd);
            if ($metrics['status'] === 'Present') $metrics['status'] = 'Early Leave';
        }

        // 5. Overtime String Record
        if ($totalOtMinutes > 0) {
            $oh = floor($totalOtMinutes / 60);
            $om = $totalOtMinutes % 60;
            $metrics['overtime_hours'] = "{$oh} hours {$om} min";
        }

        return $metrics;
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:ausers,user_id',
            'date' => 'required|date',
            'punch_in' => 'required',
            'punch_out' => 'nullable',
            'latitude' => 'required',
            'longitude' => 'required',
            'notes' => 'nullable|string',
        ]);

        $punchIn = Carbon::parse($request->date . ' ' . $request->punch_in);
        $punchOut = $request->punch_out ? Carbon::parse($request->date . ' ' . $request->punch_out) : null;
        
        $staff = Auser::find($request->user_id);
        $metrics = $this->calculateAttendanceMetrics($staff, $request->date, $punchIn, $punchOut);

        Attendance::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'punch_in' => $punchIn,
            'punch_out' => $punchOut,
            'status' => $metrics['status'],
            'total_hours' => $metrics['total_hours'],
            'overtime_hours' => $metrics['overtime_hours'],
            'late_minutes' => $metrics['late_minutes'],
            'early_exit_minutes' => $metrics['early_exit_minutes'],
            'earned_salary' => $metrics['earned_salary'],
            'basic_earned' => $metrics['basic_earned'],
            'overtime_earned' => $metrics['overtime_earned'],
            'punch_in_lat' => $request->latitude,
            'punch_in_long' => $request->longitude,
            'punch_in_device' => $request->header('User-Agent') . ' (Admin Manual)',
            'punch_in_ip' => $request->ip(),
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Attendance record created successfully.');
    }

    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $data = [
            'id' => $attendance->id,
            'user_id' => $attendance->user_id,
            'date' => $attendance->date,
            'punch_in' => $attendance->punch_in ? Carbon::parse($attendance->punch_in)->format('H:i') : '',
            'punch_out' => $attendance->punch_out ? Carbon::parse($attendance->punch_out)->format('H:i') : '',
            'notes' => $attendance->notes,
        ];
        
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'punch_in' => 'required',
            'punch_out' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $attendance = Attendance::findOrFail($id);

        $punchIn = Carbon::parse($request->date . ' ' . $request->punch_in);
        $punchOut = $request->punch_out ? Carbon::parse($request->date . ' ' . $request->punch_out) : null;

        $staff = $attendance->user;
        $metrics = $this->calculateAttendanceMetrics($staff, $request->date, $punchIn, $punchOut, $attendance->id);

        $attendance->update([
            'date' => $request->date,
            'punch_in' => $punchIn,
            'punch_out' => $punchOut,
            'total_hours' => $metrics['total_hours'],
            'overtime_hours' => $metrics['overtime_hours'],
            'late_minutes' => $metrics['late_minutes'],
            'early_exit_minutes' => $metrics['early_exit_minutes'],
            'earned_salary' => $metrics['earned_salary'],
            'basic_earned' => $metrics['basic_earned'],
            'overtime_earned' => $metrics['overtime_earned'],
            'status' => $metrics['status'],
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Attendance record updated successfully.');
    }

    // Staff View: Personal Attendance & Punch Card
    public function staffIndex()
    {
        $staff = Auth::guard('admin')->user();
        $today = date('Y-m-d');
        
        $attendance = Attendance::where('user_id', $staff->user_id)
                                ->where('date', $today)
                                ->whereNull('punch_out')
                                ->latest()
                                ->first();
        
        $history = Attendance::where('user_id', $staff->user_id)->orderBy('date', 'desc')->take(10)->get();
        
        $monthlyEarned = Attendance::where('user_id', $staff->user_id)
                                   ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                                   ->sum('earned_salary');
        
        return view('admin.attendance.personal', compact('attendance', 'history', 'staff', 'monthlyEarned'));
    }

    public function punchIn(Request $request)
    {
        $staff = Auth::guard('admin')->user();
        $today = date('Y-m-d');

        $active = Attendance::where('user_id', $staff->user_id)->where('date', $today)->whereNull('punch_out')->first();
        if ($active) {
            return response()->json(['status' => 'error', 'message' => 'You already have an active session running.']);
        }

        $metrics = $this->calculateAttendanceMetrics($staff, $today, now(), null);

        Attendance::create([
            'user_id' => $staff->user_id,
            'date' => $today,
            'punch_in' => now(),
            'punch_in_lat' => $request->lat,
            'punch_in_long' => $request->long,
            'punch_in_device' => $request->header('User-Agent'),
            'punch_in_ip' => $request->ip(),
            'status' => $metrics['status'],
            'late_minutes' => $metrics['late_minutes'],
            'earned_salary' => $metrics['earned_salary'],
            'basic_earned' => $metrics['basic_earned'],
            'overtime_earned' => $metrics['overtime_earned'],
            'total_hours' => $metrics['total_hours'],
            'notes' => $request->notes
        ]);

        return response()->json(['status' => 'success', 'message' => 'Punched in successfully at ' . now()->format('h:i A')]);
    }

    public function punchOut(Request $request)
    {
        $staff = Auth::guard('admin')->user();
        $today = date('Y-m-d');

        $attendance = Attendance::where('user_id', $staff->user_id)->where('date', $today)->whereNull('punch_out')->latest()->first();
        if (!$attendance) {
            return response()->json(['status' => 'error', 'message' => 'No active punch-in record found to close']);
        }

        if ($attendance->punch_out) {
            return response()->json(['status' => 'error', 'message' => 'Already punched out for today']);
        }

        $metrics = $this->calculateAttendanceMetrics($staff, $today, $attendance->punch_in, now(), $attendance->id);

        $attendance->update([
            'punch_out' => now(),
            'punch_out_lat' => $request->lat,
            'punch_out_long' => $request->long,
            'punch_out_device' => $request->header('User-Agent'),
            'punch_out_ip' => $request->ip(),
            'total_hours' => $metrics['total_hours'],
            'overtime_hours' => $metrics['overtime_hours'],
            'late_minutes' => $metrics['late_minutes'],
            'early_exit_minutes' => $metrics['early_exit_minutes'],
            'earned_salary' => $metrics['earned_salary'],
            'basic_earned' => $metrics['basic_earned'],
            'overtime_earned' => $metrics['overtime_earned'],
            'status' => $metrics['status'],
            'notes' => $attendance->notes . ($request->notes ? " | " . $request->notes : "")
        ]);

        return response()->json(['status' => 'success', 'message' => 'Punched out successfully. Total Duration: ' . $metrics['total_hours']]);
    }
}
