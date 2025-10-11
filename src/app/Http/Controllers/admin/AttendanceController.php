<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

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
        return view('admin.attendances.show');
    }
}
