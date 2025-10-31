<?php

namespace Tests\Feature\Admin\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class IndexTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /** @var User */
    protected $adminUser;
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2025-10-20 12:00:00');

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->actingAs($this->adminUser);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_admin_user_can_check_correctly_all_of_selected_day_attendance()
    {
        $attendances = Attendance::factory()->clockedOut()->count(5)->create();

        foreach($attendances as $attendance)
        {
            $this->get(route('admin.attendances.index'))
                ->assertSeeText($attendance->user->name)
                ->assertSeeText(Carbon::parse($attendance->clock_in)->format('H:i'))
                ->assertSeeText(Carbon::parse($attendance->clock_out)->format('H:i'));
        }
    }

    public function test_current_day_is_shown_when_transitioning()
    {
        $response = $this->get(route('admin.attendances.index'));
        $response->assertSeeText('2025/10/20');
    }

    public function test_previous_day_attendance_is_shown_when_clicking_prev_day()
    {
        $attendances = Attendance::factory()
            ->clockedOut()
            ->count(3)
            ->state(['work_date' => '2025-10-19'])
            ->create();

        $response = $this->get(route('admin.attendances.index', [
            'date' => Carbon::parse('2025-10-19')->toDateString(),
        ]));
        $response->assertSeeText('2025/10/19');

        foreach ($attendances as $attendance)
        {
            $response->assertSeeText($attendance->user->name);
            $response->assertSeeText(Carbon::parse($attendance->clock_in)->format('H:i'));
            $response->assertSeeText(Carbon::parse($attendance->clock_out)->format('H:i'));
        }
    }

    public function test_next_day_attendance_is_shown_when_clicking_next_day()
    {
        $attendances = Attendance::factory()
            ->clockedOut()
            ->count(3)
            ->state(['work_date' => '2025-10-21'])
            ->create();

        $firstAccessDate = Carbon::parse('2025-10-20');

        $this->get(route('admin.attendances.index', ['date' => $firstAccessDate->toDateString() ]))
            ->assertSeeText('2025/10/20');

        $response = $this->get(route('admin.attendances.index', [
            'date' => $firstAccessDate->addDay()->toDateString(),
        ]));

        $response->assertSeeText('2025/10/21');

        foreach ($attendances as $attendance) {
            $response->assertSeeText($attendance->user->name);
            $response->assertSeeText(Carbon::parse($attendance->clock_in)->format('H:i'));
            $response->assertSeeText(Carbon::parse($attendance->clock_out)->format('H:i'));
        }
    }
}
