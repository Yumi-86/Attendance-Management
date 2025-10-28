<?php

namespace Tests\Feature\General\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Application;
use Illuminate\Support\Carbon;

class CorrectionRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /** @var User */
    protected $user;
    protected $attendance;
    protected $adminUser;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2025-10-20 12:00:00');

        $this->user = User::factory()->create();
        $this->attendance = Attendance::factory()->clockedOut()->create([
            'user_id' => $this->user->id,
        ]);
        $this->adminUser = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($this->user);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_validation_error_when_clock_in_is_after_clock_out()
    {
        $response = $this->get(route('attendance.show', $this->attendance));
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);

        $response = $this->post(route('attendance_request.store', $this->attendance), [
            'applied_clock_in' => '19:00',
            'applied_clock_out' => '18:00',
            'applied_break_start' => ['12:00'],
            'applied_break_end' => ['12:30'],
            'applied_remarks' => 'test request',
        ]);
        $response->assertSessionHasErrors([
            'applied_clock_out' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    public function test_validation_error_when_break_start_is_after_clock_out()
    {
        $response = $this->post(route('attendance_request.store', $this->attendance), [
            'applied_clock_in' => '09:00',
            'applied_clock_out' => '18:00',
            'applied_break_start' => ['18:30'],
            'applied_break_end' => ['19:00'],
            'applied_remarks' => 'test request',
        ]);
        $response->assertSessionHasErrors([
            'applied_break_start.0' => '休憩時間が不適切な値です'
        ]);
    }

    public function test_validation_error_when_applied_remarks_is_missing()
    {
        $response = $this->post(route('attendance_request.store', $this->attendance), [
            'applied_clock_in' => '09:00',
            'applied_clock_out' => '18:00',
            'applied_break_start' => ['12:00'],
            'applied_break_end' => ['12:30'],
            'applied_remarks' => '',
        ]);
        $response->assertSessionHasErrors([
            'applied_remarks' => '備考を記入してください'
        ]);
    }
    public function test_update_request_process_is_executed_with_correct_information()
    {
        $application = [
            'applied_clock_in' => '09:00',
            'applied_clock_out' => '18:00',
            'applied_break_start' => ['12:00'],
            'applied_break_end' => ['12:30'],
            'applied_remarks' => 'test request',
        ];

        $response = $this->post(route('attendance_request.store', $this->attendance), $application);
        $response->assertRedirect(route('attendance.index'));

        $this->assertDatabaseHas('applications', [
            'user_id' => $this->user->id,
            'attendance_id' => $this->attendance->id,
            'applied_remarks' => 'test request',
        ]);

        $createdApplication = Application::first();

        $this->actingAs($this->adminUser);
        $this->get(route('admin.requests.show', $createdApplication))
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('12:00')
            ->assertSee('12:30')
            ->assertSee('test request');

        $this->get(route('admin.requests.index', ['tab' => 'pending']))
            ->assertSee('承認待ち')
            ->assertSee($this->user->name)
            ->assertSee('2025/10/20')
            ->assertSee('test request');
    }

    public function test_all_own_requests_are_shown_in_request_list()
    {
        $attendances = Attendance::factory()
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

        foreach ($attendances as $i => $attendance) {
            $this->post(route('attendance_request.store', $attendance->id), [
                'applied_clock_in' => '10:00',
                'applied_clock_out' => '19:00',
                'applied_break_start' => ['13:00'],
                'applied_break_end' => ['13:30'],
                'applied_remarks' => 'test request' . ($i + 1),
            ]);
        }

        $response = $this->get(route('attendance_request.index'));
        $response->assertSeeText('test request1');
        $response->assertSeeText('test request2');
        $response->assertSeeText('test request3');
    }

    public function test_all_requests_approved_by_admin_are_shown_approved_tab()
    {
        $application = Application::create([
            'user_id' => $this->user->id,
            'approved_by' => $this->adminUser->id,
            'attendance_id' => $this->attendance->id,
            'applied_clock_in' => '10:00',
            'applied_clock_out' => '19:00',
            'applied_remarks' => 'test request',
            'status' => 'approved'
        ]);

        $response = $this->get(route('attendance_request.index', ['tab' => 'approved']));
        $response->assertSeeText('承認済み');
        $response->assertSeeText($this->user->name);
        $response->assertSeeText('2025/10/20');
        $response->assertSeeText('test request');
    }

    public function test_transitioning_detail_page_when_clicking_detail_button_for_each_application()
    {
        $this->post(route('attendance_request.store', $this->attendance), [
            'applied_clock_in' => '10:00',
            'applied_clock_out' => '19:00',
            'applied_break_start' => ['13:00'],
            'applied_break_end' => ['13:30'],
            'applied_remarks' => 'test request'
        ])
            ->assertRedirect(route('attendance.index'));

        $response = $this->get(route('attendance_request.index'));
        $response->assertSeeText('詳細');

        $response = $this->get(route('attendance.show', $this->attendance));
        $response->assertSeeText('test request');
        $response->assertSeeText('勤怠詳細');
        $response->assertSeeText('承認待ちのため修正はできません');
    }
}
