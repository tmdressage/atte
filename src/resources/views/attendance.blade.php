@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance__content">
    <div class="date">
        @foreach($dates as $date)
        <div class="date-body">
            {{ $dates->links('vendor.pagination.date-previouspage') }}
            <h3 class="date-text">{{ $date->date }}</h3>{{ $dates->links('vendor.pagination.date-nextpage') }}
        </div>
        @endforeach
    </div>
    <div class="attendance-table">
        <table class="attendance-table__inner">
            <tr class="attendance-table__row">
                <th class="attendance-table__header-name">名前</th>
                <th class="attendance-table__header">勤務開始</th>
                <th class="attendance-table__header">勤務終了</th>
                <th class="attendance-table__header">休憩時間</th>
                <th class="attendance-table__header">勤務時間</th>
            </tr>
            @foreach ($users as $user)
            <tr class="attendance-table__row">
                <td class="attendance-table__item-name">{{ $user->name }}</td>
                <td class="attendance-table__item">{{ $user->work_start_at }}</td>
                <td class="attendance-table__item">{{ $user->work_end_at }}</td>
                <td class="attendance-table__item">{{ $user->total_rest_time }}</td>
                <td class="attendance-table__item">{{ $user->total_work_time }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="page">
        {{ $users->links() }}
    </div>
</div>
@endsection