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
        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => fn (array $attribute) => $this->generateBreakTime($attribute)['start'],
            'break_end' => fn (array $attribute) => $this->generateBreakTime($attribute)['end'],
        ];
    }

    private function generateBreakTime($attribute)
    {
        $clockIn = isset($attribute['attendance'])
            ? Carbon::parse($attribute['attendance']->clock_in)
            : Carbon::createFromTime(9, 0, 0);

        $clockOut = isset($attribute['attendance'])
            ? Carbon::parse($attribute['attendance']->clock_in)
            : Carbon::createFromTime(18, 0, 0);

        $breakStart = $clockIn->copy()->addHours(rand(2, 5));
        $breakEnd = (clone $breakStart)->addMinutes(rand(30, 60));

        if ($breakEnd->gt($clockOut)) {
            $breakEnd = $clockOut->copy()->subMinutes(15);
        }

        return [
            'start' => $breakStart->format('H:i:s'),
            'end' => $breakEnd->format('H:i:s'),
        ];
    }
}
