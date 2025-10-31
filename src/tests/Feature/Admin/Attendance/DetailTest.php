<?php

namespace Tests\Feature\Admin\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
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
    protected $adminUser;
    protected $attendance;
    
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2025-10-20 12:00:00');

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->attendance = Attendance::factory()->clockedOut()->create();

        $this->actingAs($this->adminUser);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_selected_information_is_shown_correctly_at_attendance_detail()
    {
        $response = $this->get(route('admin.attendances.show', $this->attendance));
        $response->assertSeeText($this->attendance->user->name);
        $response->assertSeeText('2025年');
        $response->assertSeeText('10月20日');
        $response->assertSee(Carbon::parse($this->attendance->clock_in)->format('H:i'));
        $response->assertSee(Carbon::parse($this->attendance->clock_out)->format('H:i'));
    }

    public function test_validation_error_when_clock_in_is_after_clock_out()
    {
        $response = $this->patch(route('admin.attendances.update', $this->attendance), [
            'applied_clock_in' => '19:00',
            'applied_clock_out' => '18:00',
        ]);
        $response->assertSessionHasErrors(['applied_clock_out' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_validation_error_when_break_start_is_after_clock_out()
    {
        $response = $this->patch(route('admin.attendances.update', $this->attendance), [
            'applied_clock_in' => '09:00',
            'applied_clock_out' => '18:00',
            'applied_break_start' => ['19:00'],
            'applied_break_end' => ['19:30'],
        ]);
        $response->assertSessionHasErrors(['applied_break_start.0' => '休憩時間が不適切な値です']);
    }

    public function test_validation_error_when_break_end_is_after_clock_out()
    {
        $response = $this->patch(route('admin.attendances.update', $this->attendance), [
            'applied_clock_in' => '09:00',
            'applied_clock_out' => '18:00',
            'applied_break_start' => ['17:30'],
            'applied_break_end' => ['19:00'],
        ]);
        $response->assertSessionHasErrors(['applied_break_end.0' => '休憩時間もしくは退勤時間が不適切な値です']);
    }


    public function test_validation_error_when_applied_remarks_is_missing()
    {
        $response = $this->patch(route('admin.attendances.update', $this->attendance), [
            'applied_clock_in' => '09:00',
            'applied_clock_out' => '18:00',
            'applied_remarks' => '',
        ]);
        $response->assertSessionHasErrors(['applied_remarks' => '備考を記入してください']);
    }
}
