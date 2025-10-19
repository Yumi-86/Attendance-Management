<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index(Request $request) {
        $activeTab = $request->get('tab', 'pending');

        $pendingApplications = Application::with(['user', 'attendance'])
            ->where('status', 'pending')
            ->get();

        $approvedApplications = Application::with([ 'user','attendance'])
            ->where('status', 'approved')
            ->get();

        return view('admin.application.index', compact(
            'activeTab',
            'pendingApplications',
            'approvedApplications'
        ));
    }

    public function show(Request $request, Application $application) {
        $application->load(['attendance.breakTimes', 'user', 'application_breaks']);

        $attendance = $application->attendance;
        $user = $application->user;

        return view('admin.application.show', compact('application', 'attendance', 'user'));
    }

    public function approve(Request $request, Application $application) {
        $attendance = Attendance::findOrFail($application->attendance_id);

        DB::transaction(function () use ($attendance, $application, $request) {
            $attendance->update([
                'clock_in' => $application->applied_clock_in,
                'clock_out' => $application->applied_clock_out,
            ]);

            $attendance->breakTimes()->delete();

            foreach($application->application_breaks as $break) {
                $attendance->breakTimes()->create([
                    'break_start' => $break->applied_break_start,
                    'break_end' => $break->applied_break_end,
                ]);
            }

            $application->update([
                'approved_by' => Auth::id(),
                'status' => 'approved',
            ]);
        });

        return redirect()
            ->route('admin.requests.show', $application)
            ->with('success', '申請を承認しました');
    }
}
