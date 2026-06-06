<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use App\Models\like;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    // 4	商品一覧取得 全商品
    public function test_item_list_page_displays_all_items(): void{
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $response = $this->get('/');
        $response->assertStatus(200);
        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    // 4	商品一覧取得 購入済みSold表示
    public function test_sold_status_is_displayed_on_index_page(): void{
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $soldItem = Item::first();

        Purchase::create([
            'user_id'           => $user->id,
            'item_id'           => $soldItem->id,
            'price'             => $soldItem->price,
            'payment_method'    => 'card',
            'shipping_postcode' => '000-0000',
            'shipping_address'  => '東京都渋谷区',
        ]);

        $orderedItem = Item::where('id', '!=', $soldItem->id)->first();
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    // 4	商品一覧取得 自分の商品は表示しない
	public function test_own_items_are_not_displayed_on_index_page(): void{
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = \App\Models\User::where('email', 'test@example.com')->first();
        $user->update([
            'email_verified_at' => now(),
            'name'              => 'テスト太郎',
            'postcode'          => '123-4567',
            'address'           => '東京都渋谷区',
            'profile_image'     => 'profile_images/test_avatar.jpg',
        ]);
        $myOwnItem = Item::first();
        $myOwnItem->update(['user_id' => $user->id]);
        $otherUserItem = Item::where('user_id', '!=', $user->id)->first();
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee($otherUserItem->name);
        $response->assertDontSee($myOwnItem->name);
    }

    // 7	商品詳細情報取得
	public function test_item_detail_page_displays_all_necessary_information(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = \App\Models\User::where('email', 'test@example.com')->first();
        $item = Item::first();

        like::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);

        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'シーダーの商品に対するテストコメントです。',
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $response->assertSee($item->name);
        $response->assertSee($item->brand_name);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->description);
        $response->assertSee($item->condition);
        $response->assertSee($item->img_url);

        $response->assertSee($user->name);
        $response->assertSee('シーダーの商品に対するテストコメントです。');
    }
    
    // 7	商品詳細情報取得 カテゴリ
    public function test_item_detail_page_displays_multiple_selected_categories(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $item = Item::first();
        $categories = \App\Models\Category::take(2)->get();
        $item->categories()->attach($categories->pluck('id'));
        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    // 6	商品検索機能 「商品名」で部分一致検索ができる
    public function test_items_can_be_searched_by_name_partial_match(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $searchKeyword = 'サンプルの本';

        $matchItem = Item::create([
            'name'         => 'これはサンプルの本です', 
            'price'        => 1500,
            'description'  => 'テスト用の説明文',
            'condition'    => '良好',
            'user_id'      => $user->id,
        ]);

        $unmatchItem = Item::create([
            'name'         => '全く関係のない高級腕時計', 
            'price'        => 98000,
            'description'  => 'テスト用の説明文',
            'condition'    => '新品',
            'user_id'      => $user->id,
        ]);

        $response = $this->get('/?keyword=' . urlencode($searchKeyword));
        $response->assertStatus(200);
        $response->assertSee($matchItem->name);
        $response->assertDontSee($unmatchItem->name);
    }

    // 6	商品検索機能 検索状態がマイリストでも保持されている
	public function test_search_keyword_is_retained_when_switching_to_mylist(): void{
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
        $seller = User::where('id', '!=', $user->id)->first() ?? \App\Models\User::factory()->create();

        $searchKeyword = '便利グッズ';
        $matchMylistItem = Item::create([
            'name'         => 'これは超便利グッズです',
            'price'        => 2000,
            'description'  => '説明文',
            'condition'    => 1,
            'user_id'      => $seller->id,
        ]);

        $unmatchMylistItem = Item::create([
            'name'         => '普通のキッチンタイマー',
            'price'        => 800,
            'description'  => '説明文',
            'condition'    => 1,
            'user_id'      => $seller->id,
        ]);

        like::create(['user_id' => $user->id, 'item_id' => $matchMylistItem->id]);
        like::create(['user_id' => $user->id, 'item_id' => $unmatchMylistItem->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=' . urlencode($searchKeyword));
        $response->assertStatus(200);
        $response->assertSee($matchMylistItem->name);
        $response->assertDontSee($unmatchMylistItem->name);
    }
}