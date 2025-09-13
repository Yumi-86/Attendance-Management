@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/email.css') }}">
@endsection

@section('content')
@if (session('status') == 'verification-link-sent')
<div class="status">
    確認メールを再送しました。
</div>
@endif
<div class="verify__content">
    <div class="verify__text">
        <p> 。</p>
        <p>メール認証を完了してください。</p>
    </div>
    <div class="verify__button">
        <a href="http://localhost:8025" target="_blank" class="verify__button-submit btn">認証はこちらから</a>
    </div>
    <form method="POST" action="{{ route('verification.send') }}" class="verify__resend">
        @csrf
        <button type="submit" class="verify__resend-submit"> 認証メールを再送する</button>
    </form>
</div>
@endsection