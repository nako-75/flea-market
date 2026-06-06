<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\like;
use App\Models\Comment;

class InteractionTest extends TestCase
{
    use RefreshDatabase;

    // 8	いいね機能 
	public function test_item_can_be_liked_and_count_increases(): void{
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
        like::where('item_id', $item->id)->delete();
        $response = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $likeResponse = $this->actingAs($user)->post(route('like.store', ['item_id' => $item->id]));
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $finalResponse = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $finalResponse->assertStatus(200);
        $finalResponse->assertSee('1');
    }

    // 8	いいね機能 色の変化
    public function test_like_icon_changes_color_when_liked(): void{
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
        Like::where('item_id', $item->id)->delete();

        $initialResponse = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $initialResponse->assertSee('img/like-white.png');
        $initialResponse->assertDontSee('img/like-pink.png');

        $this->actingAs($user)->post(route('like.store', ['item_id' => $item->id]));
        $finalResponse = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $finalResponse->assertStatus(200);

        $finalResponse->assertSee('img/like-pink.png');
        $finalResponse->assertDontSee('img/like-white.png');
    }

    // 8	いいね機能 解除
    public function test_item_can_be_unliked_and_count_decreases(): void{
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
        Like::where('item_id', $item->id)->delete();
        $this->actingAs($user)->post(route('like.store', ['item_id' => $item->id]));
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->actingAs($user)->post(route('like.store', ['item_id' => $item->id]));
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $finalResponse = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $finalResponse->assertStatus(200);
        $finalResponse->assertSee('0');
        $finalResponse->assertSee('img/like-white.png');
        $finalResponse->assertDontSee('img/like-pink.png');
    }

    // 9	コメント送信機能 ログインユーザーはコメントを送信できる
	public function test_authenticated_user_can_send_comment(): void{
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
        Comment::where('item_id', $item->id)->delete();
	
        $commentData = [
                'comment' => 'テスト用のコメントメッセージです。購入を検討しています！',
            ];
        $response = $this->actingAs($user)->post(route('comment.store', ['item_id' => $item->id]), $commentData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テスト用のコメントメッセージです。購入を検討しています！',
        ]);
        $finalResponse = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $finalResponse->assertStatus(200);

        $finalResponse->assertSee('1');
        $finalResponse->assertSee('テスト用のコメントメッセージです。購入を検討しています！');
    }

    // 9	コメント送信機能 未ログインだとコメントを送信できない
    public function test_guest_user_cannot_send_comment(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $item = Item::first();
        Comment::where('item_id', $item->id)->delete();
        $commentData = [
            'comment' => 'ログインせずに送るコメントです。弾かれるはず！',
        ];

        $response = $this->post(route('comment.store', ['item_id' => $item->id]), $commentData);
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'ログインせずに送るコメントです。弾かれるはず！',
        ]);
    }

    // 9	コメント送信機能 255文字以上バリデーション
    public function test_comment_fails_validation_if_exceeds_max_length(): void{
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
        $item = Item::first();
        $longComment = str_repeat('あ', 256);
        $commentData = [
            'comment' => $longComment,
        ];
        $response = $this->actingAs($user)->post(route('comment.store', ['item_id' => $item->id]), $commentData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
    }

    // 9	コメント送信機能 未入力バリデーション
    public function test_comment_fails_validation_if_empty(): void{
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckProfileSetup::class,
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        ]);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::where('email', 'test@example.com')->first();
        $user->update([
            'name' => 'テストユーザー',
            'postcode' => '123-4567',
            'address' => '東京都港区',
            'building' => 'テストビル',
            'profile_image' => 'profile_images/initial_avatar.jpg',
            'email_verified_at' => now(),
        ]);
        $item = Item::first();
        $commentData = [
            'comment' => '',
        ];
        $response = $this->actingAs($user)->post(route('comment.store', ['item_id' => $item->id]), $commentData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
        $response = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('コメントを入力してください');
    }
}
