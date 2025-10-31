<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Application;
use App\Models\User;
use App\Models\Attendance;

class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Application::class;

    public function definition()
    {
        $attendance = Attendance::factory()->clockedOut()->create();

        return [
            'user_id' => $attendance->user_id,
            'attendance_id' => $attendance->id,
            'applied_clock_in' => '09:30:00',
            'applied_clock_out' => '18:30:00',
            'applied_remarks' => $this->faker->sentence(),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }
}
