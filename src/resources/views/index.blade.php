@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection


@section('content')

<div class="index__content">
    <div class="tab__group">
        <a href="/?tab=all" class="tab__item active {{ $tab == 'all' ? 'active' : '' }}">おすすめ</a>
        <a href="/?tab=mylist" class="tab__item {{ $tab == 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    <div class="item__list">
        @if($tab == 'mylist')
        @auth
            @forelse($my_items as $item)
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="item__link">
                    <div class="item__image">
                        <img src="{{ asset('storage/' . $item->img_url) }}">
                        @if($item->purchase)
                            <span class="item__sold-badge">Sold</span>
                        @endif
                    </div>
                    <p class="item__name">{{ $item->name }}</p>
                </a>
            @empty
                <p>まだ「いいね」した商品はありません。</p>
            @endforelse
        @endauth
        @else
            @foreach ($items as $item)
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                    <div class="item__image">
                        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                        @if($item->purchase)
                            <span class="item__sold-badge">Sold</span>
                        @endif
                    </div>
                    <p class="item__name">{{ $item->name }}</p>
                </a>
            @endforeach
        @endif
    </div>
</div>

@endsection