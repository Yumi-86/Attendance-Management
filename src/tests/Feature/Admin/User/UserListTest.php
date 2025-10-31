<?php

namespace Tests\Feature\Admin\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class UserListTest extends TestCase
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

    public function test_admin_user_can_check_all_general_users_name_and_email()
    {
        $users = User::factory()->count(5)->create();

        $response = $this->get(route('admin.users.index'));

        foreach ($users as $user)
        {
            $response->assertSeeText($user->name);
            $response->assertSeeText($user->email);
        }
    }

    public function test_selected_user_attendance_information_is_shown_correctly()
    {
        $selectedUser = User::factory()
            ->create();

        $attendances = Attendance::factory()->clockedOut()->count(5)->create([
            'user_id' => $selectedUser->id,
        ]);

        $response = $this->get(route('admin.users.attendances', $selectedUser));
        $response->assertSeeText($selectedUser->name);
        $response->assertSeeText('2025/10');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }

    public function test_previous_month_of_displayed_month_is_shown_by_clicking_prev_month()
    {
        $selectedUser = User::factory()->create();

        $prevAttendance = Attendance::factory()
            ->clockedOut()
            ->count(10)
            ->sequence(
                ['work_date' => '2025-09-01'],
                ['work_date' => '2025-09-02'],
                ['work_date' => '2025-09-03'],
                ['work_date' => '2025-09-04'],
                ['work_date' => '2025-09-05'],
                ['work_date' => '2025-09-06'],
                ['work_date' => '2025-09-07'],
                ['work_date' => '2025-09-08'],
                ['work_date' => '2025-09-09'],
                ['work_date' => '2025-09-10'],
            )
            ->clockedOut()
            ->create([
                'user_id' => $selectedUser->id,
            ]);
        $response = $this->get(route('admin.users.attendances', $selectedUser) . '?month=2025-09');
        $response->assertSeeText($selectedUser->name);
        $response->assertSeeText('2025/09');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }
    public function test_next_month_of_displayed_month_is_shown_by_clicking_next_month()
    {
        $selectedUser = User::factory()->create();

        $nextAttendance = Attendance::factory()
            ->count(10)
            ->sequence(
                ['work_date' => '2025-11-01'],
                ['work_date' => '2025-11-02'],
                ['work_date' => '2025-11-03'],
                ['work_date' => '2025-11-04'],
                ['work_date' => '2025-11-05'],
                ['work_date' => '2025-11-06'],
                ['work_date' => '2025-11-07'],
                ['work_date' => '2025-11-08'],
                ['work_date' => '2025-11-09'],
                ['work_date' => '2025-11-10'],
            )
            ->clockedOut()
            ->create([
                'user_id' => $selectedUser->id,
            ]);
        $response = $this->get(route('admin.users.attendances', $selectedUser) . '?month=2025-11');
        $response->assertSeeText($selectedUser->name);
        $response->assertSeeText('2025/11');
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }

    public function test_transitioning_to_attendance_detail_by_clicking_detail()
    {
        $selectedUser = User::factory()->create();

        $attendance = Attendance::factory()->clockedOut()->create([
            'user_id' => $selectedUser->id,
        ]);

        $response = $this->get(route('admin.attendances.show', $attendance));
        $response->assertSeeText($selectedUser->name);
        $response->assertSeeText('勤怠詳細');
        $response->assertSeeText('2025年');
        $response->assertSeeText('10月20日');
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);
    }
}
