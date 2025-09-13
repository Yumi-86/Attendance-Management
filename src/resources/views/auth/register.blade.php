@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
@endsection

@section('content')
<div class="auth__content">
    <div class="auth__heading auth__heading--register">
        <h2>会員登録</h2>
    </div>
    <form action="/register" method="post" class="auth__form auth__form--register" novalidate>
        @csrf
        <div class="form__group">
            <label for="name" class="form__header">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form__input">
            @error('name')
            <div class="form__error">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form__group">
            <label for="email" class="form__header">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form__input">
            @error('email')
            <div class="form__error">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form__group">
            <label for="password" class="form__header">パスワード</label>
            <input id="password" type="password" name="password" class="form__input">
            @error('password')
            @if($message !== 'パスワードと一致しません')
            <div class="form__error">
                {{ $message }}
            </div>
            @endif
            @enderror
        </div>
        <div class="form__group">
            <label for="password_confirmation" class="form__header">確認用パスワード</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form__input">
            @error('password')
            @if($message === 'パスワードと一致しません')
            <div class="form__error">
                {{ $message }}
            </div>
            @endif
            @enderror
        </div>
        <div class="form__btn">
            <button type="submit" class="btn">登録する</button>
        </div>
    </form>
    <div class="auth__link auth__link--login">
        <a href="/login">ログインはこちら</a>
    </div>
</div>
@endsection