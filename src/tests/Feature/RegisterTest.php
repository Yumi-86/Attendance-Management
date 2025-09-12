<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_redirect_to_email_verified_screen_if_not_done_yet()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/');

        $response->assertRedirect('email/verify')
            ->assertSeeText('登録していただいたメールアドレスに認証メールを送付しました');
    }
}
