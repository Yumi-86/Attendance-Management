@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/attendance_index.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <div class="attendance-list__inner">
        <div class="list__heading">
            <h2 class="list__heading-txt">勤怠一覧</h2>
        </div>
        <div class="list__content-pagenation">
            <a href="{{ route('attendance.index', ['month' => $prevMonth]) }}" class="pagenation__link pagenation__link--prev">前月</a>
            <span class="pagenation__cur">{{ $currentMonth->format('Y/m') }}</span>
            <a href="{{ route('attendance.index', ['month' => $nextMonth]) }}" class="pagenation__link pagenation__link--nxt">翌月</a>
        </div>
        <div class="list__content-main">
            <table class="attendance-table">
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
                        <td>{{ $attendance->break_time ?? '' }}</td>
                        <td>{{ $attendance->total_time ?? '' }}</td>
                        <td>
                            @if ($attendance)
                            <a href="{{ route('attendance.show', $attendance->id) }}">詳細</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection