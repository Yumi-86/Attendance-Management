<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.string' => 'メールアドレスは文字列で入力してください',
            'email.email' => 'メールアドレスの形式が正しくありません',
            'email.max' => 'メールアドレスは255文字以下で入力してください',

            'password.required' => 'パスワードを入力してください',
            'password.string' => 'パスワードは文字列で入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
        ];
    }
    protected function passedValidation()
    {
        $user = User::where('email', $this->input('email'))->first();

        if (str_starts_with($this->path(), 'admin/login') && $user && $user->role !== 'admin') {
            throw ValidationException::withMessages([
                'email' => '管理者アカウントとして認証できません',
            ]);
        }

        if ($this->routeIs('login') && $user && $user->role !== 'general') {
            throw ValidationException::withMessages([
                'email' => '一般ユーザーとして認証できません',
            ]);
        }
    }
}
