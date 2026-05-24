<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\PurchaseRequest;

class ItemController extends Controller
{
    public function index(Request $request){

        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                return redirect('/email/verify');
            }
            if (empty($user->name) || empty($user->profile_image) || empty($user->postcode)) {
                return redirect('/mypage/profile');
            }
        }

        $tab = $request->query('tab', 'all');
        $keyword = $request->query('keyword');
        $itemQuery = Item::query();
        if (Auth::check()) {
            $itemQuery->where('user_id', '!=', Auth::id());
        }
        if (!empty($keyword)){
            $itemQuery->where('name','LIKE',"%{$keyword}%");
        }

        $items = $itemQuery->get();
        $my_items = [];

        if (Auth::check()){
            $my_itemQuery = Auth::user()->likedItems();

            if (!empty($keyword)){
                $my_itemQuery->where('name','LIKE',"%{$keyword}%");
            }

            $my_items = $my_itemQuery->get();
        }
        return view('index', compact('items', 'my_items','tab','keyword'));
    }

    public function create(){
        $categories = Category::all();
        return view('sell',compact('categories'));
    }

    public function show($item_id){
        if (Auth::check()) {
            $user = Auth::user();

            if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                return redirect('/email/verify');
            }

            if (empty($user->name) || empty($user->profile_image) || empty($user->postcode)) {
                return redirect('/mypage/profile');
            }
        }

        $item = Item::with(['categories','comments.user'])
                ->withCount(['comments', 'likes'])
                ->findOrFail($item_id);
        $is_liked = false;
        if(Auth::check()){
            $is_liked = Auth::user()->likedItems()->where('item_id', $item_id)->exists();
        }
        return view('show', compact('item', 'is_liked'));
    }

    public function purchase($item_id){
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        $displayAddress = [
            'postcode' => session('shipping_postcode', $user->postcode),
            'address'  => session('shipping_address', $user->address),
            'building' => session('shipping_building', $user->building),
        ];
        return view('purchase', [
            'item' => $item,
            'user' => (object)$displayAddress
        ]);
    }

    public function store(ExhibitionRequest $request) {
        $validated = $request->validated();
        $item = new Item();
        if(auth()->check()){
            $item->user_id = auth()->id();
        }

        $item->name = $validated['name'];
        $item->price = $validated['price'];
        $item->description = $validated['description'];
        $item->condition = $validated['condition'];
        $item->brand_name = $request->brand_name;
        $item->img_url = null;
        $item->save();

        if ($request->hasFile('item_image')) {
            $file = $request->file('item_image');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'item_' . $item->id . '.' . $extension;
            $file->storeAs('item_images', $fileName, 'public');
            $item->img_url = 'item_images/' . $fileName;
            $item->save();
        }

        if ($request->has('category_ids')) {
            $item->categories()->attach($validated['category_ids']);
        }

        return redirect('/')->with('message','商品を出品しました！');
    }

    public function checkout(PurchaseRequest $request, $item_id){
        $validated = $request->validated();

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::create([
            'payment_method_types' => [$validated['payment_method']],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',

            'success_url' => route('checkout.success',['item_id' => $item_id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel', ['item_id' => $item_id]),
            'metadata' => [
                'user_id' => $user->id,
                'item_id' => $item_id,
                'price' => $item->price,
                'payment_method' => $validated['payment_method'],
                'shipping_postcode' => $validated['shipping_postcode'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_building' => $validated['shipping_building'] ?? '',
            ],
        ]);

        return redirect($session->url, 303);
    }

    public function success(Request $request, $item_id) {
            Stripe::setApiKey(config('services.stripe.secret'));
            try {
                $stripeSession = Session::retrieve($request->get('session_id'));
                $metadata = $stripeSession->metadata;

            Purchase::create([
                'user_id' => $metadata['user_id'],
                'item_id' => $metadata['item_id'],
                'price' => $metadata['price'],
                'payment_method' => $metadata['payment_method'],
                'shipping_postcode' => $metadata['shipping_postcode'],
                'shipping_address'  => $metadata['shipping_address'],
                'shipping_building' => $metadata['shipping_building'] ?? null,
            ]);

            session()->forget([
                'shipping_postcode',
                'shipping_address',
                'shipping_building'
            ]);

            return redirect('/')->with('message','商品を購入しました！');
        } catch (\Exception $e) {
            return redirect('/')->with('error', '決済処理中にエラーが発生しました。');
        }
    }
}