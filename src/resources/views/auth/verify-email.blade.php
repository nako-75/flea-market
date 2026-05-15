<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>COACHTECH FLEA MARKET</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}" />
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="/" class="header__logo">
                <img src="{{ asset('images/logo.png') }}" alt="COACHTECH">
            </a>
        </div>
    </header>

<main>
    <div class="auth-container">
        <div class="auth-message">
            <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p>メール認証を完了してください。</p>
        </div>

        <div class="auth-action">
            <a href="http://localhost:8025/" class="auth-button">
            認証はこちらから
            </a>
        </div>

        <div class="auth-resend">
            <form method="post" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="resend-link">
                    認証メールを再送する
                </button>
            </form>
        </div>
    </div>
</body>
</html>
