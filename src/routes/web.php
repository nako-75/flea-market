<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Middleware\CheckProfileSetup;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 未ログイン
Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
Route::post('/login', function (Request $request) {
    $loginRequest = new LoginRequest();
    $validator = Validator::make(
        $request->all(),
        $loginRequest->rules(),
        $loginRequest->messages()
    );

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => ['ログイン情報が登録されていません'],
    ]);
})->name('login.post');


// ログイン
Route::middleware(['auth', 'verified', CheckProfileSetup::class])->group(function () {
    Route::get('/mypage/profile', function(){
            return view('mypage.profile', ['user' => Auth::user()]);
        })->name('profile.edit');
    Route::post('/mypage/profile', [UserProfileController::class, 'store'])->name('profile.store');
    Route::post('/mypage//profile', [UserProfileController::class, 'store']);
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');
    Route::get('/item/{item_id}/purchase', [ItemController::class, 'purchase']);
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::post('/item/{item_id}/like', [LikeController::class, 'store'])
        ->name('like.store')
        ->middleware('auth');
    Route::get('/purchase/{item_id}', [ItemController::class, 'purchase'])->name('item.purchase');
    Route::post('/purchase/{item_id}', [ItemController::class, 'storePurchase']);
    Route::get('/purchase/address/{item_id}', [UserProfileController::class, 'editPurchaseAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [UserProfileController::class, 'updatePurchaseAddress'])->name('purchase.address.update');
    Route::post('/purchase/checkout/{item_id}', [ItemController::class, 'checkout'])->name('checkout');
    Route::get('/purchase/success/{item_id}', [ItemController::class, 'success'])->name('checkout.success');
    Route::get('/purchase/cancel/{item_id}', function($item_id) {
            return redirect()->route('item.purchase', ['item_id' => $item_id]);
        })->name('checkout.cancel');
});


