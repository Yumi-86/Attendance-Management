<?php

namespace Tests\Feature\General\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;

class DetailTest extends TestCase
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

    public function test_attendance_detail_displays_logged_in_user_name()
    {
        $attendance = Attendance::factory()->clockedOut()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('attendance.show', $attendance));

        $response->assertOk();
        $response->assertSeeText($this->user->name);
    }

    public function test_attendance_detail_displays_selected_date()
    {
        $attendance = Attendance::factory()->clockedOut()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('attendance.show', $attendance));

        $response->assertOk();
        $response->assertSeeText('2025年');
        $response->assertSeeText('10月20日');
    }

    public function test_attendance_detail_displays_correct_clock_in_and_out_times()
    {
        $attendance = Attendance::factory()->clockedOut()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('attendance.show', $attendance));

        $response->assertOk();
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);
    }

    public function test_attendance_detail_displays_correct_break_times()
    {
        $attendance = Attendance::factory()
            ->clockedOut()
            ->has(BreakTime::factory())
            ->create([
                'user_id' => $this->user->id,
            ]);

        $response = $this->get(route('attendance.show', $attendance));

        $response->assertOk();
        $response->assertSee('value="12:00"', false);
        $response->assertSee('value="12:30"', false);
    }
}
