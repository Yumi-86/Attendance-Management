<?php

namespace Tests\Feature\General\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class EmailVerifyTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
        $response->assertRedirect('/email/verify');
    }

    public function test_user_can_access_verification_site()
    {
        /**@var User */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $this->actingAs($user);

        $response = $this->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSeeText('認証はこちらから');
        $response->assertSee('http://localhost:8025');
    }

    public function test_user_can_verify_email_and_redirect_to_attendance_page()
    {
        /** @var User */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user);

        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('attendance.create', ['verified' => 1]));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
