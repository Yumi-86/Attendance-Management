<?php

namespace App\Http\Controllers\general;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Application;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = Carbon::parse($request->get('month', now()->format('Y-m')));
        $start = $currentMonth->copy()->startOfMonth();
        $end = $currentMonth->copy()->endOfMonth();

        $period = CarbonPeriod::create($start, $end);

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', auth()->id())
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->work_date)->toDateString());

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        return view('general.attendance.index', compact(
            'period', 'attendances', 'currentMonth', 'prevMonth', 'nextMonth'
        ));
    }

    public function create() {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('work_date', today())
            ->first();

        $status = $attendance?->status ?? 'off_duty';

        $today = CarbonImmutable::now()->isoFormat('YYYY年M月D日(ddd)');
        $now = Carbon::now()->format('H:i');

        return view('general.attendance.create', compact(['attendance','status', 'today','now']));
    }
    public function clockIn(Request $request) {
        $userId = $request->user()->id;

        $attendance = Attendance::todayRecord($userId);

        if($attendance) {
            return back()->with('error', '今日の出勤はすでに記録されています。');
        }

        Attendance::create([
            'user_id' => $userId,
            'work_date' =>today(),
            'clock_in' => now(),
            'status' => 'working',
        ]);

        return redirect()->route('attendance.create')->with('success', '出勤しました');
    }

    public function startBreak(Request $request) {
        $userId = $request->user()->id;
        $attendance = Attendance::todayRecord($userId);

        if(! $attendance) {
            return back()->with('error', '出勤が記録されていません');
        }

        if( $attendance->status !== 'working') {
            return back()->with('error', '出勤中でなければ休憩できません');
        }

        $lastBreak = $attendance->breakTimes()->latest()->first();

        if ($lastBreak && is_null($lastBreak->break_end)) {
            return back()->with('error', '前の休憩がまだ終了していません');
        }

        DB::transaction(function () use ($attendance) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => now(),
            ]);
            $attendance->update([
                'status' => 'on_break',
            ]);
        });

        return redirect()->route('attendance.create')->with('success', '休憩を開始しました');
    }

    public function endBreak(Request $request) {
        $userId = $request->user()->id;

        $attendance = Attendance::todayRecord($userId);

        if(! $attendance) {
            return back()->with('error', '出勤が記録されていません');
        }

        if( $attendance->status !== 'on_break') {
            return back()->with('error', '休憩中ではありません');
        }

        DB::transaction(function () use ($attendance) {
            $break = $attendance->breakTimes()
                ->whereNull('break_end')
                ->latest('break_start')
                ->first();

            if($break){
                $break->update([
                    'break_end' => now(),
                ]);
            }

            $attendance->update([
                'status' => 'working',
            ]);

        });

        return redirect()->route('attendance.create')->with('success', '休憩を終了しました');
    }

    public function clockOut(Request $request) {
        $userId = $request->user()->id;
        $attendance = Attendance::todayRecord($userId);

        if(! $attendance) {
            return back()->with('error', '出勤が記録されていません');
        }

        if( $attendance->status !== 'working') {
            return back()->with('error', '出勤中ではありません');
        }

        $attendance->update([
            'clock_out' => now(),
            'status' => 'clocked_out',
        ]);

        return redirect()->route('attendance.create')->with('success', '退勤しました');
    }

    public function show(Attendance $attendance) {
        $this->authorize('view', $attendance);

        $user = Auth::user();
        $breaks = BreakTime::where('attendance_id', $attendance->id)->get();
        $application = Application::with('application_breaks')
            ->where('attendance_id', $attendance->id)
            ->where('status','pending')
            ->latest()
            ->first();

        return view('general.attendance.show', compact('user','attendance','breaks', 'application'));
    }
}
