@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')

<div class="main__content">
    <div class="common__heading">
        <h1>住所の変更</h1>
    </div>

    <div class="auth-form__inner">
        <form action="/purchase/address/{{ $item_id }}" method="POST">
            @csrf

            {{-- 郵便番号 --}}
            <div class="auth-form__group">
                <div class="auth-form__group-title">
                    <span class="auth-form__label--item">郵便番号</span>
                </div>
                <div class="auth-form__group-content">
                    <div class="auth-form__input--text">
                        <input type="text" name="postcode" value="{{ old('postcode', Auth::user()->postcode) }}">
                    </div>
                    <div class="form__error">
                        @error('postcode')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            {{-- 住所 --}}
            <div class="auth-form__group">
                <div class="auth-form__group-title">
                    <span class="auth-form__label--item">住所</span>
                </div>
                <div class="auth-form__group-content">
                    <div class="auth-form__input--text">
                        <input type="text" name="address" value="{{ old('address', Auth::user()->address) }}">
                    </div>
                    <div class="form__error">
                        @error('address')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            {{-- 建物名 --}}
            <div class="auth-form__group">
                <div class="auth-form__group-title">
                    <span class="auth-form__label--item">建物名</span>
                </div>
                <div class="auth-form__group-content">
                    <div class="auth-form__input--text">
                        <input type="text" name="building" value="{{ old('building', Auth::user()->building) }}">
                    </div>
                    <div class="form__error">
                        @error('building')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ボタン --}}
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
</div>

@endsection