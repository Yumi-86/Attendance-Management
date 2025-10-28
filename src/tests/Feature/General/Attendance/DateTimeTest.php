<?php

namespace Tests\Feature\General\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Carbon;

class DateTimeTest extends TestCase
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

    public function test_datetime_on_screen_match_with_current_datetime()
    {
        $response = $this->get(route('attendance.create'));

        $response->assertSeeText('2025年10月20日(月)');
        $response->assertSeeText('12:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
