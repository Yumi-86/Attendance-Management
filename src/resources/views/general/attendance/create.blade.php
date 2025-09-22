@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/attendance_create.css') }}">
@endsection

@section('content')
<div class="attendance__content">
    @if(session('error'))
    <div class="session__status session__status--error">
        {{ session('error') }}
    </div>
    @endif
    @if(session('success'))
    <div class="session__status session__status--success">
        {{ session('success') }}
    </div>
    @endif

    <div class="attendance__main">
        <div class="attendance__status">
            <label class="attendance__status-label">
                {{ config('constants.attendance.status.' . $status)}}
            </label>
        </div>
        <div class="attendance__date-time">
            <div class="attendance__date">{{ $today }}</div>
            <div class="attendance__time">{{ $now }}</div>
        </div>

        @if($status === 'off_duty')
        <div class="attendance__btn">
            <form action="{{ route('attendance.clockIn') }}" method="POST" class="attendance__btn-form">
                @csrf
                <button type="submit" class="attendance__btn-submit attendance__btn-submit--clock">出勤</button>
            </form>
        </div>
        @elseif($status === 'working')
        <div class="attendance__btn attendance__btn--working">
            <form action="{{ route('attendance.startBreak') }}" method="POST" class="attendance__btn-form">
                @csrf
                <button type="submit" class="attendance__btn-submit attendance__btn-submit--break">休憩入</button>
            </form>
            <form action="{{ route('attendance.clockOut') }}" method="POST" class="attendance__btn-form">
                @csrf
                <button type="submit" class="attendance__btn-submit attendance__btn-submit--clock">退勤</button>
            </form>
        </div>
        @elseif($status === 'breaking')
        <div class="attendance__btn">
            <form action="{{ route('attendance.endBreak') }}" method="POST" class="attendance__btn-form">
                @csrf
                <button type="submit" class="attendance__btn-submit attendance__btn-submit--break">休憩戻</button>
            </form>
        </div>
        @elseif($status === 'finished_work')
        <p>お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection