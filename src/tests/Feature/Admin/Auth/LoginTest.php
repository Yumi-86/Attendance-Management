<?php

namespace Tests\Feature\Admin\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'role' => 'admin',
            'password' => bcrypt('password')
        ]);
    }

    public function test_validation_error_when_email_is_missing()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_validation_error_when_password_is_missing()
    {
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    public function test_validation_error_when_unregistered_information_is_entered()
    {
        $response = $this->post('/admin/login', [
            'email' => 'different@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません。',
        ]);
    }

    public function test_user_can_login_with_correct_information()
    {
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertRedirect(route('admin.attendances.index'));
    }
}
