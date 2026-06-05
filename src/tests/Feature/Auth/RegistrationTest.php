<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function validData(){
        return [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
    }

    public function test_validation_fails_if_name_is_missing(){
        $data = $this->validData();
        $data['name'] = '';
        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors('name');
    }

    public function test_validation_fails_if_email_is_missing(){
        $data = $this->validData();
        $data['email'] = '';
        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors('email');
    }

    public function test_validation_fails_if_password_is_missing(){
        $data = $this->validData();
        $data['password'] = '';
        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors('password');
    }

    public function test_validation_fails_if_password_is_shorter_than_8_characters(){
        $data = $this->validData();
        $data['password'] = '1234567';
        $data['password_confirmation'] = '1234567';
        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors('password');
    }

    public function test_validation_fails_if_password_confirmation_does_not_match(){
        $data = $this->validData();
        $data['password'] = 'password123';
        $data['password_confirmation'] = 'mismatch';
        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors('password');
    }
}
