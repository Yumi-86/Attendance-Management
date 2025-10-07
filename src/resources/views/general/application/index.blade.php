@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/attendance_application_index.css') }}">
@endsection

@section('content')
<div class="application-list">
    <div class="application-list__inner">
        <div class="application-list__heading">
            <h2 class="application-list__heading-txt">申請一覧</h2>
        </div>
        <div class="application-list__tabs">
            <a href="{{ route('attendance_request.index') }}?tab=pending" class="application-list__tab {{ $activeTab === 'pending' ? 'application-list__tab--active':''}}">承認待ち</a>
            <a href="{{ route('attendance_request.index') }}?tab=approved" class="application-list__tab {{ $activeTab === 'approved' ? 'application-list__tab--active':'' }}">承認済み</a>
        </div>
        <div class="application-list__content">
            @if($activeTab === 'pending')
                <table class="application-list__table">
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                    @forelse($pendingApplications as $application)
                    <tr>
                        <td>{{ config('constants.application.status.' . $application->status ) }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $application->attendance->work_date->format('Y/m/d') }}</td>
                        <td>{{ $application->applied_remarks }}</td>
                        <td>{{ ($application->created_at)->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendance.show', $application->attendance) }}">詳細</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="application-list__no-data">承認待ちの申請はありません</td>
                    </tr>
                    @endforelse
                </table>
            @elseif($activeTab === 'approved')
                <table class="application-list__table">
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                    @forelse($approvedApplications as $application)
                    <tr>
                        <td>{{ config('constants.application.status.' . $application->status ) }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $application->attendance->work_date->format('Y/m/d') }}</td>
                        <td>{{ $application->applied_remarks }}</td>
                        <td>{{ ($application->created_at)->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendance.show', $application->attendance) }}">詳細</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="application-list__no-data">承認済みの申請はありません</td>
                    </tr>
                    @endforelse
                </table>
            @endif
        </div>
    </div>
</div>
@endsection