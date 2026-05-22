<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class ItemController extends Controller
{
    public function index(Request $request){

        if (Auth::check()) {
            $user = Auth::user();
            if (empty($user->name) || empty($user->profile_image) || empty($user->postcode)) {
                    $previousUrl = url()->previous();
                    if (str_contains($previousUrl, 'login')) {
                    } else {
                        return redirect('/mypage/profile');
                }
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
        return view('purchase', compact('item'));
    }

    public function store(Request $request) {
        $imagePath = null;
        if ($request->hasFile('item_image')) {
            $imagePath = $request->file('item_image')->store('item_images', 'public');
        }

        $item = new Item();
        if(auth()->check()){
            $item->user_id = auth()->id();
        }

        $item->name = $request->name;
        $item->price = $request->price;
        $item->description = $request->description;
        $item->img_url = $imagePath;
        $item->condition = $request->condition;
        $item->brand_name = $request->brand_name;
        $item->save();

        if ($request->has('category_ids')) {
            $item->categories()->attach($request->category_ids);
        }

        return redirect('/')->with('message','商品を出品しました！');
    }

    public function checkout(Request $request, $item_id){
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::create([
            'payment_method_types' => [$request->payment_method],
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
                'postcode' => $user->postcode,
                'address' => $user->address,
                'building' => $user->building,
            ],
        ]);

        return redirect($session->url, 303);
    }

    public function success(Request $request, $item_id) {
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item_id,
        ]);

        return redirect('/')->with('message','商品を購入しました！');
    }
}
