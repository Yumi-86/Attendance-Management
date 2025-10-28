<?php

namespace Tests\Feature\General\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class ClockOutTest extends TestCase
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

    public function test_working_user_can_clock_out_and_status_changes_to_clocked_out()
    {
        Attendance::factory()->working()->create([
            'user_id' => $this->user->id,
        ]);
        $this->get(route('attendance.create'))
            ->assertSeeText('退勤');

        $response = $this->post(route('attendance.clockOut'));
        $response->assertRedirect(route('attendance.create'));

        $this->get(route('attendance.create'))
            ->assertSeeText('退勤済');
    }

    public function test_user_can_check_clock_out_time_at_attendance_list()
    {
        $this->post(route('attendance.clockIn'))
            ->assertRedirect(route('attendance.create'));

        Carbon::setTestNow('2025-10-20 18:00:00');

        $this->post(route('attendance.clockOut'))
            ->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('10/20 (月)');
        $response->assertSeeText('12:00');
        $response->assertSeeText('18:00');
    }
}
