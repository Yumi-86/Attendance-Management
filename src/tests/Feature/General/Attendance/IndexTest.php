<?php

namespace Tests\Feature\General\Attendance;

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

    public function test_all_attendances_that_user_made_show_at_attendance_list()
    {
        Attendance::factory()
            ->clockedOut()
            ->count(3)
            ->sequence(
                ['work_date' => '2025-10-18'],
                ['work_date' => '2025-10-19'],
                ['work_date' => '2025-10-20'],
            )
            ->create([
                'user_id' => $this->user->id,
            ]);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('10/18 (土)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
        $response->assertSeeText('10/19 (日)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
        $response->assertSeeText('10/20 (月)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }

    public function test_current_month_shows_when_transitioning_to_attendance_list()
    {
        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('2025/10');
    }

    public function test_pre_month_shows_when_clicking_pre_month()
    {
        Attendance::factory()
            ->clockedOut()
            ->count(3)
            ->sequence(
                ['work_date' => '2025-09-18'],
                ['work_date' => '2025-09-19'],
                ['work_date' => '2025-09-20'],
            )
            ->create([
                'user_id' => $this->user->id,
            ]);

        $currentMonth = Carbon::parse(now()->format('Y-m'));
        $prevMonth = (clone $currentMonth)->subMonth()->format('Y-m');

        $response = $this->get(route('attendance.index', ['month' => $prevMonth]));
        $response->assertSeeText('09/18 (土)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
        $response->assertSeeText('09/19 (日)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
        $response->assertSeeText('09/20 (月)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }

    public function test_next_month_shows_when_clicking_next_month()
    {
        Attendance::factory()
            ->clockedOut()
            ->count(3)
            ->sequence(
                ['work_date' => '2025-11-18'],
                ['work_date' => '2025-11-19'],
                ['work_date' => '2025-11-20'],
            )
            ->create([
                'user_id' => $this->user->id,
            ]);

        $currentMonth = Carbon::parse(now()->format('Y-m'));
        $nextMonth = (clone $currentMonth)->addMonth()->format('Y-m');

        $response = $this->get(route('attendance.index', ['month' => $nextMonth]));
        $response->assertSeeText('11/18 (土)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
        $response->assertSeeText('11/19 (日)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
        $response->assertSeeText('11/20 (月)');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }

    public function test_clicking_detail_button_when_transitioning_to_attendance_detail()
    {
        $attendance = Attendance::factory()->clockedOut()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('詳細');

        $response = $this->get(route('attendance.show', $attendance));
        $response->assertSeeText('勤怠詳細');
        $response->assertSeeText('2025年');
        $response->assertSeeText('10月20月');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }
}
