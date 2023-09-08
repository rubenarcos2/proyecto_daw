<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\config;

class ApiConfigTest extends TestCase
{
    /**
     * A test of api return config list
     */
    public function test_returns_all_configs(): void
    {
        $this->get('/api/config')->assertStatus(200);
    }

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
     * A test of api return a config
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_config($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/config/1')->assertStatus(200);
    }

    /**
     * A test of api return a config's users
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_config´s_users($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/config/1/users')->assertStatus(200);
    }

    /**
     * A test of api return a user's config
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_user´s_config($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/config/user/1')->assertStatus(200);
    }

    /**
     * A test of api update test config
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_update_test_config($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = config::where('name', 'tts')->get()->first();

        $payload = [
            "id" => $prod->id,
            "name" => 'tts',
            "title" => 'Texto a voz 2',
            "value" => 'false',
            "description" => 'Lee en voz alta todos los textos 2',
            "domain" => 'True/False'
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/config/update', $payload)->assertStatus(200);
    }

    /**
     * A test of api update test config
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_update_test_2_config($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = config::where('name', 'tts')->get()->first();

        $payload = [
            "id" => $prod->id,
            "name" => 'tts',
            "title" => 'Texto a voz',
            "value" => 'false',
            "description" => 'Lee en voz alta todos los textos',
            "domain" => 'True/False'
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/config/update', $payload)->assertStatus(200);
    }

    /**
     * A test of api update test config assign user
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_update_test_config_assign_user($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = config::where('name', 'tts')->get()->first();

        $payload = [
            "id" => $prod->id,
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/config/user/update/1', $payload)->assertStatus(200);
    }

    /**
     * A test of api update test config unassign user
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_update_test_config_unassign_user($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = config::where('name', 'tts')->get()->first();

        $payload = [
            "id" => $prod->id,
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/config/user/delete/1', $payload)->assertStatus(200);
    }

    /**
     * A administrator logout test.
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
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
