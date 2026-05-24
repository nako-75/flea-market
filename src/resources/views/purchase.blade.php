@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

<form action="{{ route('checkout',['item_id' => $item->id]) }}" method="post" class="purchase__form purchase__container">
    @csrf

    <input type="hidden" name="shipping_postcode" value="{{ session('shipping_postcode', Auth::user()->postcode) }}">
    <input type="hidden" name="shipping_address" value="{{ session('shipping_address', Auth::user()->address) }}">
    <input type="hidden" name="shipping_building" value="{{ session('shipping_building', Auth::user()->building) }}">

    <div class="purchase__main">
        <div class="purchase__contents">
            <div class="item__image">
                <img src="{{ asset('storage/' . $item->img_url) }}">
            </div>
            <div class="item__info">
                <h1>{{ $item->name }}</h1>
                <p>￥{{ number_format($item->price) }}</p>
            </div>
        </div>

        <div class="section payment-section">
                <h2>支払い方法</h2>
                @error('payment_method')
                    <p class="error-message">{{ $message }}</p>
                @enderror
                <select name="payment_method" onchange="document.getElementById('display-payment').innerText = this.options[this.selectedIndex].text">
                    <option value="" disabled selected>選択してください</option>
                    <option value="konbini">コンビニ支払い</option>
                    <option value="card">カード支払い</option>
                </select>
        </div>

        <div class="section">
            <h2>配送先</h2>
            <a href="/purchase/address/{{ $item->id }}" class="link-text">変更する</a>
        </div>
        <div class="address-box">
            <p>〒{{ $user->postcode }}</p>
            <p>{{ $user->address }}</p>
            <p>{{ $user->building }}</p>
        </div>
    </div>

    <div class="summary__contents">
        <div class="purchase__summary">
            <table class="summary-table">
                <tr>
                    <th>商品代金</th>
                    <td>￥{{ number_format($item->price) }}</td>
                </tr>
                <tr class="table__border-top">
                    <th>支払い方法</th>
                    <td id="display-payment">未選択</td>
                </tr>
            </table>
        </div>

        <div class="purchase__action">
            <button type="submit" class="btn-purchase">購入する</button>
        </div>
    </div>
</form>
@endsection


