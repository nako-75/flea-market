@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')

<div class="container">
    <div class="detail-flex">
        <div class="detail-left">
            <div class="item-main-image">
                <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
            </div>
        </div>

        <div class="detail-right">
            <h1 class="item-name">{{ $item->name }}</h1>
            <p class="brand-name">{{ $item->brand_name }}</p>
            <p class="item-price">¥ {{ number_format($item->price) }}<span class="tax-in">（税込）</span></p>

            <div class="action-buttons">
                <div class="status-group">
                    <form action="{{ route('like.store', ['item_id' => $item->id]) }}" method="post">
                    @csrf
                        <button type="submit" class="btn-icon">
                        @if(isset($is_liked) && $is_liked)
                            <img src="{{ asset('img/like-pink.png') }}" class="icon-img">
                        @else
                            <img src="{{ asset('img/like-white.png') }}" class="icon-img">
                        @endif
                        </button>
                    </form>
                    <p class="status-count">{{ $item->likes_count ?? 0 }}</p>
                </div>

                <div class="status-group">
                    <div class="btn-icon">
                        <img src="{{ asset('img/comment-icon.png') }}" class="icon-img">
                    </div>
                    <p class="status-count">{{ $item->comments_count ?? 0 }}</p>
                </div>
            </div>

            <a href="/item/{{ $item->id }}/purchase" class="btn-purchase-confirm">購入手続きへ</a>

            <div class="section-box">
                <h2 class="section-title">商品説明</h2>
                <p class="description-text">{{ $item->description }}</p>
            </div>

            <div class="section-box">
                <h2 class="section-title">商品の情報</h2>
                <table class="detail-table">
                    <tr>
                        <th>カテゴリー</th>
                        <td>
                            @forelse($item->categories as $category)
                                <span class="category-tag">{{ $category->name }}</span>
                            @empty
                                <span class="no-category">指定なし</span>
                            @endforelse
                        </td>
                    </tr>

                    <tr>
                        <th>商品の状態</th>
                        <td>{{ $item->condition_text }}</td>
                    </tr>
                </table>
            </div>

            <div class="comment-section">
                <h3 class="comment-count">コメント ({{ $item->comments_count ?? 0 }}) </h3>

                <div class="comment-list">
                    @foreach($item->comments as $comment)
                        <div class="comment-item">
                            <div class="comment-user">
                                <img src="{{ asset($comment->user->profile_image ? 'storage/' . $comment->user->profile_image : 'img/default-user.png') }}" alt="ユーザー" class="user-icon">
                                <span class="user-name">{{ $comment->user->name }}</span>
                            </div>

                            <div class="comment-content-box">
                                <p class="comment-text">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="comment-form-area">
                    <h3 class="form-label">商品へのコメント</h3>
                    <form action="/item/{{ $item->id }}/comment" method="post">
                        @csrf
                        <textarea name="comment" class="comment-textarea"></textarea>
                        <button type="submit" class="btn-comment-submit">コメントを送信する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection