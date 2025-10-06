<?php

namespace App\Http\Controllers\general;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceApplicationRequest;
use App\Models\Attendance;
use App\Models\Application;
use App\Models\ApplicationBreak;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceApplicationController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();

        $activeTab = $request->get('tab', 'pending');

        $pendingApplications = Application::with('attendance')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();

        $approvedApplications = Application::with('attendance')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->get();

        return view('general.application.index', compact(
            'user',
            'pendingApplications',
            'approvedApplications',
            'activeTab',
        ));
    }

    public function store(Attendance $attendance, AttendanceApplicationRequest $request) {

        $application = Application::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'applied_clock_in' => $request->input('applied_clock_in'),
            'applied_clock_out' => $request->input('applied_clock_out'),
            'applied_remarks' => $request->input('applied_remarks'),
            'status' => 'pending',
        ]);

        foreach ($request->input('applied_break_start', []) as $i => $start) {
            $end = $request->input("applied_break_end.$i");

            if (!$start || !$end) {
                continue;
            }

            ApplicationBreak::create([
                'application_id' => $application->id,
                'applied_break_start' => $start,
                'applied_break_end' => $end,
            ]);
        }

        return redirect()->route('attendance.index')->with('success', '修正申請を送信しました。');
    }
}
