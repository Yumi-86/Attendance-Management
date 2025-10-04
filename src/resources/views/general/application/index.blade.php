@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages.attendance_application_index.css') }}">
@endsection

@section('content')
<div class="application-list">
    @if(session('success'))
    <div class="application-alert">{{ session('success') }}</div>
    @endif

    <div class="application-list__inner">
        <div class="application__heading">
            <h2 class="application__heading-txt">申請一覧</h2>
        </div>
        <div class="application__content">
            <div class="application__tabs">
                <a href="{{ route('attendance_request.index') }}?tab=pending" class="{{ $activeTab === 'pending' ? 'active':''}}">承認待ち</a>
                <a href="{{ route('attendance_request.index') }}?tab=approved" class="{{ $activeTab === 'approved' ? 'active':'' }}"></a>
            </div>
            @if($activeTab === 'pending')
            <div class="application__list">
                <table class="application__list-table">
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
                        <td>{{ config(constants.application.status) . $application->status }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ ($application->work_date)->format('Y/m/d') }}</td>
                        <td>{{ $application->applied_remarks }}</td>
                        <td>{{ ($application->created_at)->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendance.show', $attendance') }}">詳細</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td>承認待ちの申請はありません</td>
                    </tr>
                    @endforelse
                </table>
            </div>
            @elseif($activeTab === 'approved')
            <div class="application__list">
                <table class="application__list-table">
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
                        <td>{{ config(constants.application.status) . $application->status }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ ($application->work_date)->format('Y/m/d') }}</td>
                        <td>{{ $application->applied_remarks }}</td>
                        <td>{{ ($application->created_at)->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendance.show', $attendance') }}">詳細</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td>承認済みの申請はありません</td>
                    </tr>
                    @endforelse
                </table>
            </div>
        </div>
    </div>
</div>