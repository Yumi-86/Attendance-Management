@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
@endsection

@section('content')

@if( session('message'))
<div class="login-message">
    {{ session('message') }}
</div>
@endif
@if (session('warning'))
<div class="login-warning">
    {{ session('warning') }}
</div>
@endif

<div class="auth__content">
    <div class="auth__heading auth__heading--login">
        <h2>ログイン</h2>
    </div>
    <form action="/login" method="post" class="auth__form auth__form--login" novalidate>
        @csrf
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
            <div class="form__error">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form__btn">
            <button type="submit" class="btn">ログインする</button>
        </div>
    </form>
    <div class="auth__link auth__link--register">
        <a href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection