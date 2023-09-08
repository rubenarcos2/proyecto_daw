<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class ApiPermissionTest extends TestCase
{
    /**
     * A test of api return permission list
     */
    public function test_returns_all_permissions(): void
    {
        $this->get('/api/permission')->assertStatus(200);
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
     * A test of api return a permission
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_permission($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/permission/1')->assertStatus(200);
    }

    /**
     * A test of api return a permission's user
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_permission´s_user($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/permission/user/1')->assertStatus(200);
    }

    /**
     * A test of api return a assign permission to user
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_assign_permission_to_user($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = Permission::where('name', 'permission-list')->get()->first();

        $payload = [
            "id" => $prod->id
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/permission/user/1')->assertStatus(200);
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
