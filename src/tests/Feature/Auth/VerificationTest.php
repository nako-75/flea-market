<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_notification_is_sent_after_registration(){
        Notification::fake();
        $user = User::factory()->create(['email_verified_at' => null]);
        $user->notify(new VerifyEmail);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_user_can_access_verification_link_from_email_notice_page(){
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertStatus(200);
    }

    public function test_user_can_verify_email_and_redirect_to_profile(){
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );
        $response = $this->actingAs($user)->get($url);
        $user->refresh();
        $response->assertRedirectContains('/mypage/profile');
    }
}
