@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/admin/users_index.css') }}">
@endsection

@section('content')
<div class="users-page">
    <div class="users-page__inner">
        <div class="users-page__heading">
            <h2 class="users-page__heading-txt">スタッフ一覧</h2>
        </div>
        <table class="users-page__table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td class="users-table__name">{{ $user->name }}</td>
                    <td class="users-table__email">{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('admin.users.attendances', $user) }}" class="users-table__link">詳細</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="users-table__no-data">スタッフが存在しません</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection