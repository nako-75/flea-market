<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\like;
use App\Models\Purchase;
use App\Models\Category;

class MyPageTest extends TestCase
{
    use RefreshDatabase;

    // 5	マイリスト一覧取得 いいねだけ表示
	public function test_only_liked_items_are_displayed_on_mylist(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::where('email', 'test@example.com')->first();
        $user->update([
            'name' => 'テストユーザー',
            'profile_image' => 'avatar.jpg',
            'postcode' => '123-4567',
            'email_verified_at' => now(),
        ]);
        $items = Item::where('user_id', '!=', $user->id)->take(2)->get();
        $likedItem   = $items[0];
        $unlikedItem = $items[1];
        like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee($likedItem->name);
        $response->assertDontSee($unlikedItem->name);
    }

    // 5	マイリスト一覧取得 購入済み「Sold」表示
    public function test_purchased_items_display_sold_label_on_mylist(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::where('email', 'test@example.com')->first();
        $user->update([
            'name' => 'テストユーザー',
            'profile_image' => 'avatar.jpg',
            'postcode' => '123-4567',
            'email_verified_at' => now(),
        ]);
        $item = Item::where('user_id', '!=', $user->id)->first();

        Purchase::create([
            'user_id'           => $user->id,
            'item_id'           => $item->id,
            'price'             => $item->price,
            'payment_method'    => 'card',
            'shipping_postcode' => '000-0000',
            'shipping_address'  => '東京都渋谷区',
        ]);

        like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    //  5	マイリスト一覧取得 未認証の場合は何も表示されない
    public function test_no_items_are_displayed_on_mylist_when_unauthenticated(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $item = Item::first();
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertDontSee($item->name);
    }

    // 15	出品商品情報登録
    public function test_item_can_be_published_with_required_information(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::where('email', 'test@example.com')->first();
        $user->update([
            'name' => 'テストユーザー',
            'profile_image' => 'avatar.jpg',
            'postcode' => '123-4567',
            'email_verified_at' => now(),
        ]);
        $categories = Category::take(2)->get();
        $categoryIds = $categories->pluck('id')->toArray();

        $imageFile = \Illuminate\Http\UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg');
        $postData = [
            'name'          => 'テスト出品の商品名',
            'brand_name'    => 'テストブランド',
            'description'   => 'これはテスト用の商品説明文です。',
            'price'         => 5500,
            'condition'     => '1',
            'category_ids'  => $categoryIds,
        ];

        $response = $this->actingAs($user)->call(
            'POST',
            route('item.store'),
            $postData,
            [],
            ['item_image' => $imageFile]
        );

        $response->assertStatus(302);

        $this->assertDatabaseHas('items', [
            'name'        => 'テスト出品の商品名',
            'brand_name'  => 'テストブランド',
            'description' => 'これはテスト用の商品説明文です。',
            'price'       => 5500,
            'condition'   => '1',
            'user_id'     => $user->id,
        ]);

        $latestItem = Item::latest('id')->first();
        foreach ($categoryIds as $catId) {
            $this->assertDatabaseHas('category_item', [
                'item_id'     => $latestItem->id,
                'category_id' => $catId,
            ]);
        }
    }
}

