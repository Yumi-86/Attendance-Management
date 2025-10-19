<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index() {
        $users = User::where('role', 'general')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function userIndex(Request $request, User $user) {
        $currentMonth = Carbon::parse($request->get('month', now()->format('Y-m')));
        $start = (clone $currentMonth)->startOfMonth();
        $end = (clone $currentMonth)->endOfMonth();

        $period = CarbonPeriod::create($start, $end);

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->work_date)->toDateString());

        $prevMonth = (clone $currentMonth)->subMonth()->format('Y-m');
        $nextMonth = (clone $currentMonth)->addMonth()->format('Y-m');

        return view('admin.users.show', compact('user','currentMonth', 'period', 'attendances', 'prevMonth', 'nextMonth'));
    }
    public function exportCsv(Request $request, User $user) {
        $month = $request->get('month', now()->format('Y-m'));
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$start, $end])
            ->get()
            ->keyBy(fn ($a) => Carbon::parse($a->work_date)->toDateString());

        $response = new StreamedResponse(function () use ($attendances, $start, $end)
        {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

            $period = CarbonPeriod::create($start, $end);

            foreach ($period as $date) {
                $key = $date->toDateString();
                $a = $attendances->get($key);

                fputcsv($handle, [
                    $date->format('Y-m-d'),
                    $a->clock_in_formatted ?? '',
                    $a->clock_out_formatted ?? '',
                    $a->total_break_minutes ?? '',
                    $a->total_work_minutes ?? '',
                ]);
            }

            fclose($handle);
        });

        $filename =$user->name . "_{$month}_勤怠.csv";
        $filename = mb_convert_encoding($filename, 'SJIS-win', 'UTF-8');

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }
}
