<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;

class UserProfileController extends Controller
{
    public function store(ProfileRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $fileName = 'user_' . $user->id . '.' . $extension;
        $file->storeAs('profiles', $fileName, 'public');
        $user->profile_image = 'profiles/' . $fileName;
    }

    $user->name = $validated['name'];
    $user->postcode = $validated['postcode'];
    $user->address = $validated['address'];
    $user->building = $request->building;
    $user->save();

    return redirect('/');
    }

    public function editPurchaseAddress($item_id){
        $user = Auth::user();
        $addressData = [
            'shipping_postcode' => session('shipping_postcode', $user->postcode),
            'shipping_address'  => session('shipping_address', $user->address),
            'shipping_building' => session('shipping_building', $user->building),
        ];

        return view('purchase_address_edit', [
            'user'    => (object)$addressData,
            'item_id' => $item_id
    ]);
    }

    public function updatePurchaseAddress(AddressRequest $request, $item_id){
        $validated = $request->validated();

        session([
            'shipping_postcode' => $validated['shipping_postcode'],
            'shipping_address'  => $validated['shipping_address'],
            'shipping_building' => $request->shipping_building,
        ]);
        return redirect()->route('item.purchase', ['item_id' => $item_id]);
    }

}