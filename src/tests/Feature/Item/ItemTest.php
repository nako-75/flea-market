<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use \App\Models\Purchase;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
            $this->user = User::factory()->create([
            'name' => 'テスト太郎',
            'profile_image' => 'dummy.jpg',
            'postcode' => '123-4567',
            'address' => '東京都渋谷区'
        ]);
        $this->otherUser = User::factory()->create(['name' => '他人']);
        $this->actingAs($this->user);
    }

    public function test_item_index_displays_all_items(){
        Item::create([
            'user_id' => $this->otherUser->id,
            'name' => 'テスト商品A',
            'price' => 100,
            'description' => '説明文',
            'img_url' => 'test.jpg',
            'condition' => 1,
            'brand_name' => 'ブランドA'
        ]);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('テスト商品A');
    }

    public function test_sold_item_displays_sold_label(){
        $item = Item::create([
            'user_id' => $this->otherUser->id,
            'name' => '売却済商品',
            'price' => 100,
            'condition' => 1,
            'description' => 'テスト',
            'img_url' => 'test.jpg',
            'brand_name' => 'テスト'
        ]);
        Purchase::create([
            'item_id' => $item->id,
            'user_id' => $this->user->id,
            'price' => 100,
            'payment_method' => 'card',
            'shipping_postcode' => '000-0000',
            'shipping_address' => '住所',
            'shipping_building' => '建物'
        ]);
        $response = $this->get('/');
        $response->assertSee('Sold');
    }

    public function test_own_items_are_not_displayed_in_index(){
        $response = $this->actingAs($this->user)->get('/');
    $response->assertDontSee('自分の商品名');
    }

    public function test_search_by_keyword(){
        Item::create(['user_id' => $this->otherUser->id, 'name' => '美味しい玉ねぎ', 'price' => 100, 'condition' => 1]);
        $response = $this->get('/?keyword=ねぎ');
        $response->assertSee('美味しい玉ねぎ');
    }

    public function test_search_keyword_is_retained_in_mylist(){
        $response = $this->actingAs($this->user)
                    ->get('/?tab=mylist&keyword=マイク');
        $response->assertStatus(200);
        $response->assertSee('name="keyword"', false);
        $response->assertSee('value="マイク"', false);
    }

    public function test_item_detail_displays_basic_info_and_comments(){
        $item = Item::create([
            'user_id' => $this->otherUser->id,
            'name' => 'テスト詳細商品',
            'price' => 500,
            'description' => '詳細説明文',
            'condition' => 1,
            'brand_name' => 'ブランド'
        ]);
        $comment = new Comment();
        $comment->item_id = $item->id;
        $comment->user_id = $this->user->id;
        $comment->comment = 'テストコメントです';
        $comment->save();

        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('テスト詳細商品');
        $response->assertSee('テストコメントです');
    }

    public function test_item_detail_displays_multiple_categories(){
        $item = Item::create(['user_id' => $this->user->id, 'name' => 'カテゴリ商品', 'price' => 100, 'condition' => 1]);
        $c1 = Category::create(['name' => 'ファッション']);
        $c2 = Category::create(['name' => '家電']);
        $item->categories()->attach([$c1->id, $c2->id]);
        $response = $this->get('/item/' . $item->id);
        $response->assertSee('ファッション');
        $response->assertSee('家電');
    }
}