@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/attendance_show.css') }}">
@endsection

@section('content')
<div class="attendance-page">
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
                        <div class="attendance-name">{{ $user->name }}</div>
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
                        <div class="attendance-remarks__txt" disabled>{{ $application->applied_remarks }}</div>
                    </td>
                </tr>
            </table>
            <div class="attendance-dtl__msg">
                <span>* 承認待ちのため修正はできません。</span>
            </div>
        </div>
        @else
        <form class="attendance-dtl__form" method="post" action="{{ route('admin.attendances.update', $attendance )}}">
            @csrf
            @method('patch')
            <table class="attendance-table">
                <tr>
                    <th>名前</th>
                    <td>
                        <div class="attendance-name">{{ $user->name }}</div>
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
                            <input type="time" name="applied_clock_in" value="{{ old('applied_clock_in', $attendance->clock_in_formatted )}}" class="attendance-clock__input">
                            <span>〜</span>
                            <input type="time" name="applied_clock_out" value="{{ old('applied_clock_out', $attendance->clock_out_formatted )}}" class="attendance-clock__input">
                        </div>
                        @error('applied_clock_in')
                        <div class="attendance-error">{{ $message }}</div>
                        @enderror
                        @error('applied_clock_out')
                        <div class="attendance-error">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                @foreach($breaks as $break)
                <tr>
                    <th>休憩{{ $loop->iteration }}</th>
                    <td>
                        <div class="attendance-break">
                            <input type="time" name="applied_break_start[]" value="{{ old('applied_break_start.' . $loop->index, $break->break_start_formatted )}}" class="attendance-break__input">
                            <span>〜</span>
                            <input type="time" name="applied_break_end[]" value="{{ old('applied_break_end.' . $loop->index, $break->break_end_formatted )}}" class="attendance-break__input">
                        </div>
                        @error('applied_break_start.' . $loop->index)
                        <div class="attendance-error">{{ $message }}</div>
                        @enderror
                        @error('applied_break_end.' . $loop->index)
                        <div class="attendance-error">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                @endforeach
                <tr>
                    <th>休憩{{ $breaks->count() + 1}}</th>
                    <td>
                        <div class="attendance-break">
                            <input type="time" name="applied_break_start[]" value="{{ old('applied_break_start.' . $breaks->count()) }}" class="attendance-break__input">
                            <span>〜</span>
                            <input type="time" name="applied_break_end[]" value="{{ old('applied_break_end.' . $breaks->count()) }}" class="attendance-break__input">
                        </div>
                        @error('applied_break_start.' . $breaks->count())
                        <div class="attendance-error">{{ $message }}</div>
                        @enderror
                        @error('applied_break_end.' . $breaks->count())
                        <div class="attendance-error">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="applied_remarks" rows="3" class="attendance-remarks attendance-remarks__input">{{ old('applied_remarks') }}</textarea>
                        @error('applied_remarks')
                        <div class="attendance-error">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            </table>
            <div class="attendance-dtl__btn">
                <button class="attendance-btn attendance-btn--submit" type="submit">修正</button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection