@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')

<div class="mypage_container">
    <div class="profile-image__content">
        <div class="user-info-wrapper">
            <div class="profile-image__preview">
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="アイコン">
            </div>
            <span class="user-name">{{ $user->name }}</span>
        </div>
        <a href="/mypage/profile" class="btn-edit">プロフィールを編集</a>
    </div>

    <div class="index__content">
        <nav class="mypage-tabs">
            <ul class="mypage__tab-list">
                <li class="mypage__tab-item {{ request('page') != 'buy' ? 'active' : '' }}">
                    <a href="/mypage?page=sell">出品した商品</a>
                </li>
                <li class="mypage__tab-item {{ request('page') == 'buy' ? 'active' : ''}}">
                    <a href="/mypage?page=buy">購入した商品</a>
                </li>
            </ul>
        </nav>

        <div class="item__list">
            @forelse ($items as $item)
                <div class="item__image">
                    <div class="item__image">
                        <img src="{{ $item->img_url ? asset('storage/' . $item->img_url) : asset('images/no-image.png') }}" alt="{{ $item->name }}">
                    </div>
                    <p class="item__name">{{ $item->name }}</p>
                </div>
            @empty
                <p class="empty-message">
                    {{ request('tab') == 'bought' ? '購入した商品はまだありません。' : '出品した商品はまだありません。' }}
                </p>
            @endforelse
        </div>
    </div>
</div>
@endsection