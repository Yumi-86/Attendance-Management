<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\User;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'work_date' => Carbon::today()->toDateString(),
            'clock_in' => null,
            'clock_out' => null,
            'status' => 'off_duty',
        ];
    }

    public function working()
    {
        return $this->state(fn () => [
            'clock_in' => '09:00:00',
            'status' => 'working'
        ]);
    }

    public function onBreak()
    {
        return $this->afterCreating(function ($attendance) {
            $attendance->breakTimes()->create([
                'break_start' =>'12:00:00',
                'break_end' => null,
            ]);
        })->state(fn () => [
            'clock_in' => '09:00:00',
            'status' => 'on_break'
        ]);
    }

    public function clockedOut()
    {
        return $this->state(fn () => [
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => 'clocked_out',
        ]);
    }
}
