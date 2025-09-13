@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
@endsection

@section('content')

<div class="auth__content">
    <div class="auth__heading auth__heading--login">
        <h2>管理者ログイン</h2>
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
            <button type="submit" class="btn">管理者ログインする</button>
        </div>
    </form>
</div>
@endsection