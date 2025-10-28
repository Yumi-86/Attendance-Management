<?php

namespace Tests\Feature\General\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;

class BreakTest extends TestCase
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

    public function test_working_user_can_take_break_and_status_changes_to_on_break()
    {
        $attendance = Attendance::factory()->working()->create([
            'user_id' => $this->user->id,
        ]);

        $this->post(route('attendance.startBreak'))
            ->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('休憩中');
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'break_start' => '12:00:00',
        ]);
    }

    public function test_user_can_take_breaks_in_a_day()
    {
        $attendance = Attendance::factory()->working()->create([
            'user_id' => $this->user->id,
        ]);

        $this->post(route('attendance.startBreak'))
            ->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('休憩戻');

        $this->post(route('attendance.endBreak'))
            ->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('休憩入');
    }

    public function test_user_on_break_can_return_to_working_status()
    {
        $attendance = Attendance::factory()->working()->create([
            'user_id' => $this->user->id,
        ]);
        $this->post(route('attendance.startBreak'))
            ->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('休憩戻');
        $response->assertSeeText('休憩中');

        $this->post(route('attendance.endBreak'))
            ->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('出勤中');
    }

    public function test_user_can_come_back_to_work_several_times_in_a_day()
    {
        $attendance = Attendance::factory()->working()->create([
            'user_id' => $this->user->id,
        ]);
        $this->post(route('attendance.startBreak'))
            ->assertRedirect(route('attendance.create'));

        $this->post(route('attendance.endBreak'))
            ->assertRedirect(route('attendance.create'));

        $this->post(route('attendance.startBreak'))
            ->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.create'));
        $response->assertSeeText('休憩戻');
    }

    public function test_user_can_check_break_time_at_attendance_list()
    {
        $attendance = Attendance::factory()->working()->create([
            'user_id' => $this->user->id,
        ]);
        $this->post(route('attendance.startBreak'))
            ->assertRedirect(route('attendance.create'));

        Carbon::setTestNow('12:30:00');

        $this->post(route('attendance.endBreak'))
            ->assertRedirect(route('attendance.create'));

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('10/20 (月)');
        $response->assertSeeText('0:30');
    }
}
