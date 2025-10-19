@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/admin/users__show.css') }}">
@endsection

@section('content')
<div class="user-attendance">

    @if (session('success'))
    <div class="user-attendance__alert">{{ session('success') }}</div>
    @endif

    <div class="user-attendance__inner">
        <div class="user-attendance__heading">
            <h2 class="user-attendance__heading-txt">{{ $user->name }}さんの勤怠</h2>
        </div>
        <div class="user-attendance__pagenation">
            <a href="{{ route('admin.users.attendances', ['user' => $user, 'month' => $prevMonth]) }}" class="pagenation__link pagenation__link--prev">前月</a>
            <span class="pagenation__cur">{{ $currentMonth->format('Y/m') }}</span>
            <a href="{{ route('admin.users.attendances', ['user' => $user, 'month' => $nextMonth]) }}" class="pagenation__link pagenation__link--nxt">翌月</a>
        </div>
        <div class="user-attendance__table">
            <table class="user-attendance__table-inner">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($period as $date)
                    @php
                    $key = $date->toDateString();
                    $attendance = $attendances->get($key);
                    @endphp
                    <tr>
                        <td>{{ $date->isoFormat('MM/DD (ddd) ') }}</td>
                        <td>{{ $attendance->clock_in_formatted ?? '' }}</td>
                        <td>{{ $attendance->clock_out_formatted ?? '' }}</td>
                        <td>{{ $attendance->total_break_minutes ?? '' }}</td>
                        <td>{{ $attendance->total_work_minutes ?? '--:--' }}</td>
                        <td>
                            @if ($attendance)
                            <a href="{{ route('admin.attendances.show', $attendance) }}">詳細</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="user-attendance__btn">
                <a href="{{ route('admin.users.attendances.csv', ['user' => $user, 'month' => $currentMonth->format('Y-m')]) }}" class="user-attendance__btn-csv">CSV出力</a>
            </div>
        </div>
    </div>
</div>
@endsection