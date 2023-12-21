@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
<div class="attendance__content">
    <div class="attendance__alert">
        <p>{{ Auth::user()->name }}さんお疲れ様です！</P>
    </div>
    <div class="container">
        @if (session('my_status'))
        <div class="attendance__alert--success">
            {{ session('my_status') }}
        </div>
        @elseif (session('error'))
        <div class="attendance__alert--danger">
            {{ session('error') }}
        </div>
        @endif
    </div>
    <div class="attendance__work-panel">
        <form class="attendance__work-start-button" action="{{route('worktime-punchIn')}}" method="post">
            @csrf
            @method('POST')
            <button class="attendance__work-start-button-submit" type="submit" id="worktime-punchIn">勤務開始</button>
        </form>
        <form class=" attendance__work-end-button" action="{{route('worktime-punchOut')}}" method="post">
            @csrf
            @method('POST')
            <button class="attendance__work-end-button-submit" type="submit" id="worktime-punchOut">勤務終了</button>
        </form>
    </div>
    <div class="attendance__rest-panel">
        <form class="attendance__rest-start-button" action="{{route('resttime-punchIn')}}" method="post">
            @csrf
            @method('POST')
            <button class="attendance__rest-start-button-submit" type="submit" id="resttime-punchIn">休憩開始</button>
        </form>
        <form class="attendance__rest-end-button" action="{{route('resttime-punchOut')}}" method="post">
            @csrf
            @method('POST')
            <button class="attendance__rest-end-button-submit" type="submit" id="resttime-punchOut">休憩終了</button>
        </form>

        @if (session('work_started'))
        <script>
            sessionStorage.setItem(['status'], ['work_started'])
        </script>
        @elseif (session('rest_started'))
        <script>
            sessionStorage.setItem(['status'], ['rest_started'])
        </script>
        @elseif (session('rest_ended'))
        <script>
            sessionStorage.setItem(['status'], ['rest_ended'])
        </script>
        @elseif(session('work_ended'))
        <script>
            sessionStorage.setItem(['status'], ['work_ended'])
        </script>
        @elseif(session('before_work_start'))
        <script>
            sessionStorage.setItem(['status'], ['before_work_start'])
        </script>
        @elseif(session('before_rest_start'))
        <script>
            sessionStorage.setItem(['status'], ['before_rest_start'])
        </script>
        @endif

        <script>
            let status = sessionStorage.getItem("status");
            if (status == ['work_started']) {
                document.getElementById('worktime-punchIn').style.color = 'grey';
                document.getElementById('worktime-punchIn').style.backgroundColor = '#f7f7f7';
                document.getElementById('worktime-punchIn').disabled = true;
                document.getElementById('resttime-punchOut').style.color = 'grey';
                document.getElementById('resttime-punchOut').style.backgroundColor = '#f7f7f7';
                document.getElementById('resttime-punchOut').disabled = true;
            } else if (status == ['rest_started']) {
                document.getElementById('worktime-punchIn').style.color = 'grey';
                document.getElementById('worktime-punchIn').style.backgroundColor = '#f7f7f7';
                document.getElementById('worktime-punchIn').disabled = true;
                document.getElementById('resttime-punchIn').style.color = 'grey';
                document.getElementById('resttime-punchIn').style.backgroundColor = '#f7f7f7';
                document.getElementById('resttime-punchIn').disabled = true;
                document.getElementById('worktime-punchOut').style.color = 'grey';
                document.getElementById('worktime-punchOut').style.backgroundColor = '#f7f7f7';
                document.getElementById('worktime-punchOut').disabled = true;
            } else if (status == ['rest_ended']) {
                document.getElementById('worktime-punchIn').style.color = 'grey';
                document.getElementById('worktime-punchIn').style.backgroundColor = '#f7f7f7';
                document.getElementById('worktime-punchIn').disabled = true;
                document.getElementById('resttime-punchOut').style.color = 'grey';
                document.getElementById('resttime-punchOut').style.backgroundColor = '#f7f7f7';
                document.getElementById('resttime-punchOut').disabled = true;
            } else if (status == ['work_ended']) {
                document.getElementById('worktime-punchIn').style.color = 'grey';
                document.getElementById('worktime-punchIn').style.backgroundColor = '#f7f7f7';
                document.getElementById('worktime-punchIn').disabled = true;
                document.getElementById('resttime-punchIn').style.color = 'grey';
                document.getElementById('resttime-punchIn').style.backgroundColor = '#f7f7f7';
                document.getElementById('resttime-punchIn').disabled = true;
                document.getElementById('worktime-punchOut').style.color = 'grey';
                document.getElementById('worktime-punchOut').style.backgroundColor = '#f7f7f7';
                document.getElementById('worktime-punchOut').disabled = true;
                document.getElementById('resttime-punchOut').style.color = 'grey';
                document.getElementById('resttime-punchOut').style.backgroundColor = '#f7f7f7';
                document.getElementById('resttime-punchOut').disabled = true;
            } else if (status == ['before_work_start']) {
                document.getElementById('resttime-punchIn').style.color = 'grey';
                document.getElementById('resttime-punchIn').style.backgroundColor = '#f7f7f7';
                document.getElementById('resttime-punchIn').disabled = true;
                document.getElementById('worktime-punchOut').style.color = 'grey';
                document.getElementById('worktime-punchOut').style.backgroundColor = '#f7f7f7';
                document.getElementById('worktime-punchOut').disabled = true;
                document.getElementById('resttime-punchOut').style.color = 'grey';
                document.getElementById('resttime-punchOut').style.backgroundColor = '#f7f7f7';
                document.getElementById('resttime-punchOut').disabled = true;
            } else if (status == ['before_rest_start']) {
                document.getElementById('worktime-punchIn').style.color = 'grey';
                document.getElementById('worktime-punchIn').style.backgroundColor = '#f7f7f7';
                document.getElementById('worktime-punchIn').disabled = true;
                document.getElementById('resttime-punchOut').style.color = 'grey';
                document.getElementById('resttime-punchOut').style.backgroundColor = '#f7f7f7';
                document.getElementById('resttime-punchOut').disabled = true;
            } else {
                sessionStorage.clear();
            }
        </script>
    </div>
</div>
@endsection