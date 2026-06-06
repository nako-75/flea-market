<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    //13	ユーザー情報取得
    public function test_profile_page_displays_all_user_info(){
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);

        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::where('email', 'test@example.com')->first();
        $user->update([
            'profile_image' => 'profile_images/test_avatar.jpg'
        ]);

        $listedItem = Item::first();
        $listedItem->update(['user_id' => $user->id]);

        $purchasedItem = Item::where('user_id', '!=', $user->id)->first();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'price'   => $purchasedItem->price, 
            'payment_method' => 'card',
            'shipping_postcode' => '000-0000',
            'shipping_address' => '東京都渋谷区',
        ]);

        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);

        $response->assertSee($user->name);
        $response->assertSee('storage/' . $user->profile_image);

        $response->assertSee($listedItem->name);
        $response = $this->actingAs($user)->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee($purchasedItem->name);
    }

    //14 ユーザー情報変更
    public function test_profile_edit_page_displays_initial_values(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);

        $user = User::factory()->create([
            'name' => '設定済みの名前',
            'email' => 'edit-test@example.com',
            'profile_image' => 'profile_images/initial_avatar.jpg',
            'postcode' => '123-4567',
            'address' => '東京都渋谷区道玄坂',
        ]);

        $response = $this->actingAs($user)
                        ->get('/mypage/profile');
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->postcode);
        $response->assertSee($user->address);
        $response->assertSee('storage/' . $user->profile_image);
    }
}