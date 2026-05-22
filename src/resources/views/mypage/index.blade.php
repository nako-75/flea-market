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
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="item__link">
                    <div class="item__image">
                        <img src="{{ $item->img_url ? asset('storage/' . $item->img_url) : asset('images/no-image.png') }}" alt="{{ $item->name }}">
                        @if($item->purchase)
                            <span class="item__sold-badge">Sold</span>
                        @endif
                    </div>
                    <p class="item__name">{{ $item->name }}</p>
                </a>
            @empty
                <p class="empty-message">
                    {{ $page == 'buy' ? '購入した商品はまだありません。' : '出品した商品はまだありません。' }}
                </p>
            @endforelse
        </div>
    </div>
</div>
@endsection