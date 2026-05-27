@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')

<div class="main__content">
    <div class="common__heading">
        <h1>商品の出品</h1>
    </div>

    <form action="{{ route('item.store') }}" method="post" enctype="multipart/form-data" class="sell-form">
        @csrf

        <div class="sell-section">
            <h2 class="sell-section__label">商品画像</h2>
            <div class="image-upload__area">
                <label class="image-upload__btn">
                    画像を選択する
                    <input type="file" name="item_image" class="image-upload__input">
                </label>
            </div>
            @error('item_image')
                <div class="error-message">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="sell-section">
            <h2 class="sell-section__title">商品の詳細</h2>

            <div class="form-group">
                <label class="form-label">カテゴリー</label>
                <div class="tag-container">
                    @foreach($categories as $category)
                        <label class="tag-label">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="tag-input">
                            <span class="tag-text">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('category_ids')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="condition" class="form-label">商品の状態</label>
                <select name="condition" id="condition" class="form-select">
                    <option value="" selected disabled>選択してください</option>
                    <option value="1">良好</option>
                    <option value="2">目立った傷や汚れなし</option>
                    <option value="3">やや傷や汚れあり</option>
                    <option value="4">状態が悪い</option>
                </select>
                @error('condition')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="sell-section">
            <h2 class="sell-section__title">商品名と説明</h2>
            <div class="form-group">
                <label class="form-label">商品名</label>
                <input type="text" name="name" class="form-input">
                @error('name')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">ブランド名</label>
                <input type="text" name="brand_name" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">商品の説明</label>
                <textarea name="description" rows="5" class="form-textarea"></textarea>
                @error('description')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">販売価格</label>
                <div class="price-input__wrapper">
                    <span class="price-unit">¥</span>
                    <input type="text" name="price" class="form-input">
                </div>
                @error('price')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">出品する</button>
        </div>
    </form>
</div>

@endsection