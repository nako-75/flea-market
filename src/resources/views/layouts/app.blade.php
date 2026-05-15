<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>COACHTECH FLEA MARKET</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="/" class="header__logo">
                <img src="{{ asset('images/logo.png') }}" alt="COACHTECH">
            </a>

            <div class="header__search">
                <input type="text" placeholder="なにをお探しですか？">
            </div>

            <nav class="header__nav">
                <ul class="header__nav-list">
                    @auth
                        <li class="header__nav-item">
                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <button type="submit" class="header__nav-link">ログアウト</button>
                            </form>
                        </li>
                        <li class="header__nav-item">
                            <a href="/mypage" class="header__nav-link">マイページ</a>
                        </li>
                        <li class="header__nav-item">
                            <a href="/sell" class="header__nav-button">出品</a>
                        </li>
                    @endauth

                    @guest
                        <li class="header__nav-item">
                            <a href="{{ route('login') }}" class="header__nav-link">ログイン</a>
                        </li>
                    @endguest
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

</body>
</html>