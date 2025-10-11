<nav class="navbar">
    <div class="navbar-menu">
        <ul>
            @if(Auth::check() && Auth::user()->role === 'general')
            <li><a href="{{ route('attendance.create') }}" class="navbar__link">勤怠</a></li>
            <li><a href="{{ route('attendance.index') }}" class="navbar__link">勤怠一覧</a></li>
            <li><a href="{{ route('attendance_request.index') }}" class="navbar__link">申請</a></li>
            @endif

            @if(Auth::check() && Auth::user()->role === 'admin')
            <li><a href="{{ route('admin.attendances.index') }}" class="navbar__link">勤怠一覧</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="navbar__link">スタッフ一覧</a></li>
            <li><a href="{{ route('admin.requests.index') }}" class="navbar__link">申請一覧</a></li>
            @endif

            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="navbar__link navbar__logout">ログアウト</button>
                    <input type="hidden" name="role" value="{{ Auth::user()->role }}">
                </form>
            </li>
        </ul>
    </div>
</nav>