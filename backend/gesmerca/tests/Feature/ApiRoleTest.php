<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ApiRoleTest extends TestCase
{
    /**
     * A test of api return role list
     */
    public function test_returns_all_roles(): void
    {
        $this->get('/api/role')->assertStatus(200);
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
     * A test of api return a role
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_role($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/role/1')->assertStatus(200);
    }

    /**
     * A test of api return a role's user
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_role´s_user($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/role/user/1')->assertStatus(200);
    }

    /**
     * A test of api create a role
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_create_a_role($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $payload = [
            "name" => 'Test role',
            "guard_name" => 'api'
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/role/create', $payload)->assertStatus(200);
    }

    /**
     * A test of api update test role
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_update_test_role($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = Role::where('name', 'Test role')->get()->first();

        $payload = [
            "id" => $prod->id,
            "name" => 'Test role 2',
            "guard_name" => 'api'
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/role/update/'.$prod->id, $payload)->assertStatus(200);
    }

    /**
     * A test of api delete test role
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_delete_test_role($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = Role::where('name', 'Test role 2')->get()->first();

        $payload = [
            "id" => $prod->id
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/role/delete/'.$prod->id, $payload)->assertStatus(200);
    }

    /**
     * A test of api return a assign role to user
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_assign_role_to_user($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = Role::where('name', 'Admin')->get()->first();

        $payload = [
            "id" => $prod->id
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/role/user/1')->assertStatus(200);
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
