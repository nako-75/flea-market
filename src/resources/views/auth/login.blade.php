@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection


@section('content')

<div class="main__content">
    <div class="common__heading">
        <h1>ログイン</h1>
    </div>


    <div class="auth-form__inner">
        <form class="form" action="/login" method="post">
            @csrf

    {{-- アドレス --}}
            <div class="auth-form__group">
                <div class="auth-form__group-title">
                    <span class="auth-form__label--item">メールアドレス</span>
                </div>
                <div class="auth-form__group-content">
                    <div class="auth-form__input--text">
                        <input type="email" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="form__error">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            {{-- パスワード --}}
            <div class="auth-form__group">
                <div class="auth-form__group-title">
                    <span class="auth-form__label--item">パスワード</span>
                </div>
                <div class="auth-form__group-content">
                    <div class="auth-form__input--text">
                        <input type="password" name="password">
                    </div>
                    <div class="form__error">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ボタン --}}
            <div class="form__button">
                <button class="form__button-submit" type="submit">ログインする</button>
            </div>

            <a class="auth__link" href="{{ route('register') }}">会員登録はこちら</a>

        </form>
    </div>
</div>
@endsection