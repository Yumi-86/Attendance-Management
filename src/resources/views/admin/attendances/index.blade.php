@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/admin/attendance_index.css') }}">
@endsection

@section('content')
<div class="attendance-list">

    @if (session('success'))
    <div class="attendance-alert">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
    <div class="attendance-alert">{{ session('warning') }}</div>
    @endif

    <div class="attendance-list__inner">
        <div class="list__heading">
            <h2 class="list__heading-txt">{{ $work_date->format('Y年m月d日') }}の勤怠</h2>
        </div>
        <div class="list__content-pagenation">
            <a href="{{ route('admin.attendances.index', ['date' => $work_date->copy()->subDay()->toDateString()]) }}" class="pagenation__link pagenation__link--prev">前日</a>
            <span class="pagenation__cur">{{ $work_date->format('Y/m/d') }}</span>
            <a href="{{ route('admin.attendances.index', ['date' => $work_date->copy()->addDay()->toDateString() ]) }}" class="pagenation__link pagenation__link--nxt">翌日</a>
        </div>
        <table class="list__table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                @php
                $attendance = $user->attendances->first();
                @endphp
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $attendance?->clock_in_formatted ?? '' }}</td>
                    <td>{{ $attendance?->clock_out_formatted ?? '' }}</td>
                    <td>{{ $attendance?->total_break_minutes ?? '' }}</td>
                    <td>{{ $attendance?->total_work_minutes ?? '' }}</td>
                    <td>
                        @if ($attendance)
                        <a href="{{ route('admin.attendances.show', $attendance) }}">詳細</a>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="list__no-data">{{ $work_date->format('Y年m月d日') }}の勤怠データはありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection