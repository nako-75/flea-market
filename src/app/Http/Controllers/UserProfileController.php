<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class UserProfileController extends Controller
{
    public function store(Request $request)
    {
    $user = Auth::user();
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('profiles', 'public');
        $user->profile_image = $path;
    }

    $user->name = $request->name;
    $user->postcode = $request->postcode;
    $user->address = $request->address;
    $user->building = $request->building;
    $user->save();

    return redirect('/');
    }

    public function editPurchaseAddress($item_id){
        $user = Auth::user();
        return view('purchase_address_edit', [
            'user' => $user,
            'item_id' => $item_id
        ]);
    }

    public function updatePurchaseAddress(Request $request, $item_id){
        $user = Auth::user();
        $user->update([
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
        ]);
        return redirect()->route('item.purchase', ['item_id' => $item_id]);
    }

}