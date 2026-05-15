@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection


@section('content')

<div class="main__content">
    <div class="common__heading">
        <h1>プロフィール設定</h1>
    </div>

    <div class="auth-form__inner">
        <form class="form" action="/mypage/profile" method="post" enctype="multipart/form-data">
            @csrf

            {{-- 画像 --}}
            <div class="auth-form__group">
                <div class="profile-image__content">
                    <div class="profile-image__preview" id="imagePreview">
                        <img id="previewImage"
                            src="{{ (isset($user) && $user->profile_image) ? asset('storage/' . $user->profile_image) : '' }}"
                            alt="プレビュー"
                            style="{{ (isset($user) && $user->profile_image) ? '' : 'display: none;' }}">
                    </div>
                    <label class="profile-image__button">
                        画像を選択する
                        <input type="file" name="image" class="profile-image__input" id="imageInput" onchange="window.previewImage(this)">
                    </label>
                </div>
            </div>


            {{-- 名前 --}}
            <div class="auth-form__group">
                <div class="auth-form__group-title">
                    <span class="auth-form__label--item">ユーザー名</span>
                </div>
                <div class="auth-form__group-content">
                    <div class="auth-form__input--text">
                        <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}">
                    </div>
                    <div class="form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

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

<script>
    window.previewImage = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('previewImage');
                if (img) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    };
</script>
@endsection