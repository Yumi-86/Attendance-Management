<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\Application;
use App\Http\Requests\AttendanceApplicationRequest;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request) {
        $work_date = Carbon::parse($request->get('date', now()->toDateString()));

        $attendances = Attendance::with('user')
            ->dailyAttendanceSearch($work_date)
            ->get();

        return view('admin.attendances.index', compact('work_date', 'attendances'));
    }

    public function show(Attendance $attendance) {
        $user = $attendance->user;
        $breaks = BreakTime::where('attendance_id', $attendance->id)->get();
        $application = Application::with('application_breaks')
            ->where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->latest()
            ->first();
        return view('admin.attendances.show', compact('user', 'attendance', 'breaks', 'application'));
    }

    public function update(Attendance $attendance, AttendanceApplicationRequest $request) {
        $validated = $request->validated();

        DB::transaction(function () use ($attendance, $request, $validated) {
            $attendance->update([
                'clock_in' => $validated['applied_clock_in'],
                'clock_out' => $validated['applied_clock_out'],
            ]);

            $application = $attendance->applications()->create([
                'user_id' => $attendance->user_id,
                'applied_clock_in' => $validated['applied_clock_in'],
                'applied_clock_out' => $validated['applied_clock_out'],
                'applied_remarks' => $validated['applied_remarks'],
                'status' => 'approved',
            ]);

            $attendance->breakTimes()->delete();

            $starts = $request->input('applied_break_start', []);
            $ends = $request->input('applied_break_end', []);

            foreach ($starts as $i => $start) {
                $end = $ends[$i] ?? null;
                if($start && $end) {
                    $attendance->breakTimes()->create([
                        'break_start' => $start,
                        'break_end' => $end,
                    ]);

                    $application->application_breaks()->create([
                        'applied_break_start' => $start,
                        'applied_break_end' => $end,
                    ]);
                }
            }
        });
        return redirect()
            ->route('admin.attendances.show', $attendance)
            ->with('success', '勤怠管理を修正しました');
    }
}
