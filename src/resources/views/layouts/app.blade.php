<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH勤怠管理アプリ</title>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/base/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/navbar.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <div class="header-logo">
                <a href="
                        @auth
                            @if (auth()->user()->role === 'admin')
                                {{ route('admin.attendances.index') }}
                            @else
                                {{ route('attendance.create') }}
                            @endif
                        @else
                            {{ route('login') }}
                        @endauth
                " class="header-logo__link">
                    <img src="{{ asset('images/coachtech-logo.svg') }}" alt="coachtechロゴ" class="header-logo__img">
                </a>
            </div>
            @if(!Request::is('login') && !Request::is('register') && !Request::is('email/verify') && !Request::is('admin/login'))
            @include('components.navbar')
            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>