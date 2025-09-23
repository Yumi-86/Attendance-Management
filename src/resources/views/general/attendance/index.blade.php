@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/attendance_index') }}">
@endsection

@section('content')
<div class="attendance-list">
    <div class="attendance-list__inner">
        <div class="list__heading"></div>
        <div class="list__content">
            <div class="list__content-pagenation"></div>
            <div class="list__content-main"></div>
        </div>
    </div>
</div>