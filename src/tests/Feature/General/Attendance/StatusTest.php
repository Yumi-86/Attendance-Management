<?php

namespace Tests\Feature\General\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class StatusTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /** @var User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2025-10-20 12:00:00');

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_user_off_duty_status_is_displayed()
    {
        $response = $this->get(route('attendance.create'));

        $response->assertSeeText('勤務外');
    }

    public function test_user_working_status_is_displayed()
    {
        Attendance::factory()->working()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('attendance.create'));

        $response->assertSeeText('出勤中');
    }

    public function test_user_on_break_status_is_displayed()
    {
        Attendance::factory()->onBreak()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('attendance.create'));

        $response->assertSeeText('休憩中');
    }

    public function test_user_clocked_out_status_is_displayed()
    {
        Attendance::factory()->clockedOut()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('退勤済');
    }
}
