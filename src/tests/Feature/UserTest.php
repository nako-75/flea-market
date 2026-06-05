<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            \App\Http\Middleware\CheckProfileSetup::class
        ]);

            $this->user = User::factory()->create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'profile_image' => 'dummy.jpg',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'postcode' => '123-4567',
            'address' => '東京都渋谷区'
        ]);

        $this->be($this->user);

        $this->item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => 1,
            'description' => 'テスト用の説明文です',
            'img_url' => 'test_image.jpg',
            'brand_name' => 'テストブランド',
            'user_id' => $this->user->id
        ]);

        $this->boughtItem = Item::create([
            'name' => '購入商品',
            'price' => 2000,
            'condition' => 1,
            'description' => '購入の説明',
            'img_url' => 'buy.jpg',
            'brand_name' => 'ブランドB',
            'user_id' => $this->user->id+1
        ]);
    }

    public function test_profile_page_displays_all_user_info(){
        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->profile_image);
        $response->assertSee($this->item->name);
        $response->assertSee('storage/' . $this->item->img_url);
        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);
    }

    public function test_profile_edit_displays_initial_values(){
        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->postcode);
        $response->assertSee($this->user->address);
    }

    public function test_shipping_address_can_be_changed_and_reflected_in_purchase_screen(){
        $this->actingAs($this->user)->post("/purchase/address/{$this->item->id}", [
            'shipping_postcode' => '111-1111',
            'shipping_address' => '変更後の住所'
        ]);

        $response = $this->get("/purchase/{$this->item->id}");
        $response->assertSee('111-1111');
        $response->assertSee('変更後の住所');
    }

    public function test_purchase_completes_successfully(){
        $this->actingAs($this->user)
            ->post("/purchase/checkout/{$this->item->id}", [
                'price' => 2000,
                'payment_method' => 'card',
                'shipping_postcode' => '000-0000',
                'shipping_address' => '東京都渋谷区'
            ]);
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'payment_method' => 'card',
            'price' => $this->item->price,
        ]);
    }

    public function test_sold_status_is_displayed_on_index_page(){
        $this->actingAs($this->user)
            ->post("/purchase/checkout/{$this->item->id}");
        $response = $this->get('/');
        $response->assertSee('Sold');
    }

    public function test_purchased_item_appears_in_profile(){
        $this->actingAs($this->user)
            ->post("/purchase/checkout/{$this->item->id}");
        $response = $this->get('/mypage?page=buy');
        $response->assertSee($this->item->name);
    }

    public function test_payment_method_is_reflected_in_purchase_screen(){
        $this->actingAs($this->user)
            ->post("/purchase/payment/{$this->item->id}", [
                'payment_method' => 'カード払い'
            ]);

        $response = $this->get("/purchase/{$this->item->id}");
        $response->assertSee('カード払い');
    }

    public function test_purchase_records_shipping_address_correctly(){
        $this->actingAs($this->user)
        ->withSession(['shipping_address' => '変更後の住所'])
        ->post("/purchase/checkout/{$this->item->id}");
        $lastPurchase = \App\Models\Purchase::latest()->first();
        $this->assertEquals('変更後の住所', $lastPurchase->shipping_address);
        $this->assertEquals($this->item->id, $lastPurchase->item_id);
    }
}
