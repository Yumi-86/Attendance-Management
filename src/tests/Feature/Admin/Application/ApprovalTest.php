<?php

namespace Tests\Feature\Admin\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Carbon;

class ApprovalTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /**@var User */
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

    public function test_pending_applications_are_displayed()
    {
        $pendingApps = Application::factory()->count(3)->pending()->create();
        $approvedApps = Application::factory()->count(2)->approved()->create();

        $response = $this->get(route('admin.requests.index', ['tab' => 'pending']));

        foreach ($pendingApps as $app) {
            $response->assertSeeText($app->applied_remarks);
        }

        foreach ($approvedApps as $app) {
            $response->assertDontSeeText($app->applied_remarks);
        }
    }

    public function test_approved_applications_are_displayed()
    {
        $approvedApps = Application::factory()->count(2)->approved()->create();
        $pendingApps = Application::factory()->count(3)->pending()->create();

        $response = $this->get(route('admin.requests.index', ['tab' => 'approved']));

        foreach ($approvedApps as $app) {
            $response->assertSeeText($app->applied_remarks);
        }
        foreach ($pendingApps as $app) {
            $response->assertDontSeeText($app->applied_remarks);
        }
    }

    public function test_application_details_are_displayed_correctly()
    {
        $application = Application::factory()->pending()->create();

        $response = $this->get(route('admin.requests.show', $application));

        $response->assertSeeText($application->attendance->user->name);
        $response->assertSeeText(Carbon::today()->format('Y年'));
        $response->assertSeeText(Carbon::today()->format('n月j日'));
        $response->assertSeeText(Carbon::parse($application->applied_clock_in)->format('H:i'));
        $response->assertSeeText(Carbon::parse($application->applied_clock_out)->format('H:i'));
    }

    public function test_application_can_be_approved_and_updates_attendance()
    {
        $application = Application::factory()->pending()->create([
            'applied_clock_in' => '09:30:00',
            'applied_clock_out' => '18:30:00',
        ]);

        $response = $this->post(route('admin.requests.approve', $application));
        $response->assertRedirect(route('admin.requests.show', $application));
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'approved_by' => $this->adminUser->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('attendances', [
            'id' => $application->attendance_id,
            'clock_in' => $application->applied_clock_in,
            'clock_out' => $application->applied_clock_out,
        ]);

        $response = $this->get(route('admin.requests.show', $application));
        $response->assertSeeText('承認済み');
        $response->assertSeeText('09:30');
        $response->assertSeeText('18:30');
        $response->assertSeeText($application->applied_remarks);
    }
}
