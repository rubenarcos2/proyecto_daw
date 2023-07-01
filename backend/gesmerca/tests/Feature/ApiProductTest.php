<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;

class ApiProductTest extends TestCase
{
    /**
     * A test of api return product list
     */
    public function test_returns_all_products(): void
    {
        $this->get('/api/product')->assertStatus(200);
    }

    /**
     * A test of api return a product
     */
    public function test_returns_a_product(): void
    {
        $this->get('/api/product/1')->assertStatus(200);
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
     * A test of api create a product
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_create_a_product($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $payload = [
            "name" => 'Test product',
            "description" => 'A test product description',
            "image" => null,
            "price" => 9.99,
            "stock" => 99
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/product/create', $payload)->assertStatus(200);
    }

    /**
     * A test of api delete test product
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_update_test_product($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = Product::where('name', 'Test product')->get()->first();

        $payload = [
            "id" => $prod->id,
            "name" => 'Test product 2',
            "description" => 'A test product description 2',
            "image" => null,
            "price" => 1.99,
            "stock" => 11
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/product/update', $payload)->assertStatus(200);
    }

    /**
     * A test of api delete test product
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_delete_test_product($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = Product::where('name', 'Test product 2')->get()->first();

        $payload = [
            "id" => $prod->id
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/product/delete', $payload)->assertStatus(200);
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
