<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ApiAuthTest extends TestCase
{  
    /**
     * A administrator login test.
     */
    public function test_login_admin()
    {
        $admin = User::where('name', 'Administrador')->get()->first();

        $payload = [
            "email" => $admin->email,
            "password" => 'administrador',
        ];
 
        return $this->withHeaders(['Accept' => 'application/json'])->post('api/auth/login', $payload)->assertStatus(200);
    }

    /**
     * A token refresh test.
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_refresh_token($jsonResponse)
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];
        return $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('api/auth/refresh')->assertStatus(200);
    }

    /**
     * A user profile test.
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_refresh_token
     * 
     */
    public function test_user_profile($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];
        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('api/auth/user-profile')->assertStatus(200);
    }

    /**
     * A users test.
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_refresh_token
     * 
     */
    public function test_users($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];
        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('api/auth/users')->assertStatus(200);
    }

    /**
     * A administrator logout test.
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_refresh_token
     * 
     */
    public function test_logout_admin($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];
        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('api/auth/logout')->assertStatus(200);
    }
}
