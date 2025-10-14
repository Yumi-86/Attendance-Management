<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testUser = User::where('email', 'test@example.com')->first();

        $users = User::factory(20)->create();
        $users->push($testUser);

        foreach ($users as $user) {
            for ($i = 0; $i < 30; $i++) {
                $date = now()->subDays($i);

                $clockIn = Carbon::createFromTime(rand(8, 10), rand(0, 59));
                $clockOut = (clone $clockIn)->addHours(rand(7, 9))->addMinutes(rand(0, 59));

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'work_date' => $date->toDateString(),
                    'clock_in' => $clockIn->format('H:i:s'),
                    'clock_out' => $clockOut->format('H:i:s'),
                    'status' => 'clocked_out',
                ]);

                $breakCount = ($user->id === $testUser->id)
                    ? rand(1, 3)
                    : rand(1, 3);

                $usedTimes = [];

                for ($b = 0; $b < $breakCount; $b++) {
                    $start = (clone $clockIn)->addMinutes(rand(60, 360));
                    $end = (clone $start)->addMinutes(rand(30, 90));

                    if ($end > $clockOut) continue;

                    if (collect($usedTimes)->contains(fn($t) => $start->between($t['start'], $t['end']))) {
                        continue;
                    }

                    $usedTimes[] = ['start' => $start, 'end' => $end];

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $start->format('H:i:s'),
                        'break_end' => $end->format('H:i:s'),
                    ]);
                }
            }
        }
    }
}
