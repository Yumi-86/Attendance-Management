<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use Carbon\Carbon;

class BreakTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start = Carbon::parse('12:00:00');
        $end = (clone $start)->addMinutes(30);

        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => $start->format('H:i:s'),
            'break_end' => $end->format('H:i:s'),
        ];
    }

    public function ongoing()
    {
        return $this->state(fn() => [
            'break_start' => '12:00:00',
            'break_end' => null,
        ]);
    }
}
