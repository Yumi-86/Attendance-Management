<nav class="navar">
    <div class="navbar-menu">
        <ul>
            @if(Auth::check() && Auth::user()->role === 'general')
            <li><a href="{{ route('attendance.create') }}" class="navbar__link">勤怠</a></li>
            <li><a href="{{ route('attendance.index') }}" class="navbar__link">勤怠一覧</a></li>
            <li><a href="{{ route('request.index') }}" class="navbar__link">申請</a></li>
            @endif

            @if(Auth::check() && Auth::user()->role === 'admin')
            <li><a href="{{ route('admin.attendance.index') }}" class="navbar__link">勤怠一覧</a></li>
            <li><a href="{{ route('admin.user.index') }}" class="navbar__link">スタッフ一覧</a></li>
            <li><a href="{{ route('admin.request.index') }}" class="navbar__link">申請一覧</a></li>
            @endif

            <li>
                <form method="POST" action="{{ route('logout') }}" class="navbar__link">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
            </li>

        </ul>
    </div>
</nav>