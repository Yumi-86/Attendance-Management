@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/attendance_show.css') }}">
@endsection

@section('content')
<div class="application-page">
    @if(session('success'))
    <div class="attendance-dtl__alert">
        {{ session('success') }}
    </div>
    @endif
    <div class="attendance-dtl">
        <div class="attendance-dtl__heading">
            <h2 class="attendance-dtl__heading-txt">勤怠詳細</h2>
        </div>

        @if( $application && $application->status === 'pending')
        <div class="attendance-dtl__block">
            <table class="attendance-table">
                <tr>
                    <th>名前</th>
                    <td>
                        @if ($application->attendance->user_id !== $application->user_id)
                        <div class="attendance-name">{{ $application->attendance->user->name }}（管理者修正）</div>
                        @else
                        <div class="attendance-name">{{ $user->name }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>
                        <div class="attendance-date">
                            <span class="attendance-date__year">{{ $attendance->year }}</span>
                            <span class="attendance-date__date">{{ $attendance->date }}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <div class="attendance-clock">
                            <span class="attendance-clock__txt">{{ $application->applied_clock_in_formatted }}</span>
                            <span>〜</span>
                            <span class="attendance-clock__txt">{{ $application->applied_clock_out_formatted }}</span>
                        </div>
                    </td>
                </tr>
                @foreach($application->application_breaks as $break)
                <tr>
                    <th>休憩{{ $loop->iteration }}</th>
                    <td>
                        <div class=" attendance-break">
                            <span class="attendance-break__txt">{{ $break->applied_break_start_formatted }}</span>
                            <span>〜</span>
                            <span class=" attendance-break__txt">{{ $break->applied_break_end_formatted }}</span>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <th>備考</th>
                    <td>
                        <span class="attendance-remarks__txt">{{ $application->applied_remarks }}</span>
                    </td>
                </tr>
            </table>
            <form method="post" action="{{ route('admin.requests.approve', $application) }}" class="attendance-dtl__btn-form">
                @csrf
                <button class="attendance-btn attendance-btn--submit" type="submit">承認</button>
            </form>
        </div>
        @else
        <div class="attendance-dtl__block">
            <table class="attendance-table">
                <tr>
                    <th>名前</th>
                    <td>
                        @if ($application->attendance->user_id !== $application->user_id)
                        <div class="attendance-name">{{ $application->attendance->user->name }}（管理者修正）</div>
                        @else
                        <div class="attendance-name">{{ $user->name }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>
                        <div class="attendance-date">
                            <span class="attendance-date__year">{{ $attendance->year }}</span>
                            <span class="attendance-date__date">{{ $attendance->date }}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <div class="attendance-clock">
                            <span class="attendance-clock__txt">{{ $application->applied_clock_in_formatted }}</span>
                            <span>〜</span>
                            <span class="attendance-clock__txt">{{ $application->applied_clock_out_formatted }}</span>
                        </div>
                    </td>
                </tr>
                @foreach($application->application_breaks as $break)
                <tr>
                    <th>休憩{{ $loop->iteration }}</th>
                    <td>
                        <div class="attendance-break">
                            <span class="attendance-break__txt">{{ $break->applied_break_start_formatted }}</span>
                            <span>〜</span>
                            <span class="attendance-break__txt">{{ $break->applied_break_end_formatted }}</span>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <th>備考</th>
                    <td>
                        <div class="attendance-remarks__txt">{{ $application->applied_remarks }}</div>
                    </td>
                </tr>
            </table>
            <div class="attendance-dtl__btn">
                <button class="attendance-btn attendance-btn--approved" disabled>承認済み</button>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection