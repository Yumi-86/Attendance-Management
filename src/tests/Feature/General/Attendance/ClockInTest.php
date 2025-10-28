<?php

namespace Tests\Feature\General\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class ClockInTest extends TestCase
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
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_off_duty_user_can_clock_in_and_status_changes_to_working ()
    {
        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('出勤');

        $response = $this->post(route('attendance.clockIn'));
        $response->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('出勤中');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'work_date' => '2025-10-20',
            'clock_in' => '12:00:00',
            'clock_out' => null,
            'status' => 'working'
        ]);
    }

    public function test_user_can_clock_in_once_in_a_day()
    {
        $attendance = Attendance::factory()->clockedOut()->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->get(route('attendance.create'));
        $response->assertDontSeeText('出勤');
    }

    public function test_user_can_check_clock_in_time_at_attendance_list()
    {
        $response = $this->post(route('attendance.clockIn'));
        $response->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('10/20 (月)');
        $response->assertSeeText('12:00');
    }
}
