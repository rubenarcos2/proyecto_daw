<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Supplier;

class ApiSupplierTest extends TestCase
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
     * A test of api return supplier list
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_all_suppliers($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/supplier')->assertStatus(200);
    }

    /**
     * A test of api return a supplier
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_supplier($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/supplier/1')->assertStatus(200);
    }

    /**
     * A test of api create a supplier
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_create_a_supplier($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $payload = [
            "cif_nif" => 'T000000001',
            "name" => 'Proveedor 1',
            "address" => 'Dirección del proveedor 1, Ciudad, País',
            "city" => 'Ciudad',
            "phone" => '123456789',
            "email" => 'user@domain.com',
            "web" => 'webpage.com',
            "notes" => 'Comentarios del proveedor'
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/supplier/create', $payload)->assertStatus(200);
    }

    /**
     * A test of api update test supplier
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_update_test_supplier($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = supplier::where('name', 'Proveedor 1')->get()->first();

        $payload = [
            "cif_nif" => 'T000000002',
            "name" => 'Proveedor 2',
            "address" => 'Dirección del proveedor 2, Ciudad, País',
            "city" => 'Ciudad',
            "phone" => '123456789',
            "email" => 'user@domain.com',
            "web" => 'webpage.com',
            "notes" => 'Comentarios del proveedor'
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/supplier/update/'.$prod->id, $payload)->assertStatus(200);
    }

    /**
     * A test of api delete test supplier
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_delete_test_supplier($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = supplier::where('name', 'Proveedor 2')->get()->first();

        $payload = [
            "id" => $prod->id
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/supplier/delete/'.$prod->id, $payload)->assertStatus(200);
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
