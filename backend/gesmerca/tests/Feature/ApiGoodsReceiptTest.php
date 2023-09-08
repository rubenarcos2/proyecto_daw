<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\GoodsReceipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ApiGoodsReceiptTest extends TestCase
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
     * A test of api return goods receipt list
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_all_goods_receipts($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/goodsreceipt/all')->assertStatus(200);
    }

    /**
     * A test of api return a goods receipt
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_goods_receipt($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/goodsreceipt/1')->assertStatus(200);
    }

    /**
     * A test of api return a goods receipt products
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     */
    public function test_returns_a_goods_receipt_products($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->get('/api/goodsreceipt/1/products')->assertStatus(200);
    }

    /**
     * A test of api create a goods receipt
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_create_a_goods_receipt($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];
        $date = Carbon::now();

        $payload = [
            "idsupplier" => 4,
            "iduser" => 1,
            "date" => Carbon::parse($date)->format('Y-m-d'),
            "time" => Carbon::parse($date)->format('h:iA'),
            "docnum" => 'T000000001',
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/goodsreceipt/create', $payload)->assertStatus(200);
    }

    /**
     * A test of api add a goods receipt's product
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_add_a_goods_receipt_product($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $gr = GoodsReceipt::where('docnum', 'T000000001')->get()->first();

        $payload = [
            "idgoodsreceipt" => $gr->id,
            "idproduct" => 1,
            "price" => 9.99
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/goodsreceipt/'.$gr->id.'/product/add', $payload)->assertStatus(200);
    }

    /**
     * A test of api delete a goods receipt's product
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_delete_a_goods_receipt_product($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $gr = GoodsReceipt::where('docnum', 'T000000001')->get()->first();

        $payload = [
            "idgoodsreceipt" => $gr->id,
            "idproduct" => 1,
            "price" => 9.99
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/goodsreceipt/'.$gr->id.'/product/delete', $payload)->assertStatus(200);
    }

    /**
     * A test of api update test goods receipt
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_update_a_goods_receipt($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];
        $date = Carbon::now();

        $prod = GoodsReceipt::where('docnum', 'T000000001')->get()->first();

        $payload = [
            "idsupplier" => 4,
            "iduser" => 1,
            "date" => Carbon::parse($date)->format('Y-m-d'),
            "time" => Carbon::parse($date)->format('h:iA'),
            "docnum" => 'T000000002'
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/goodsreceipt/update/'.$prod->id, $payload)->assertStatus(200);
    }

    /**
     * A test of api delete test goods receipt
     * 
     * This test receives the json response from login and '@depends' is the directive that links them.
     * @depends test_login_admin
     * 
     */
    public function test_delete_a_goods_receipt($jsonResponse): void
    {
        $content = $jsonResponse->getContent();
        $array = json_decode($content, true);
        $token = $array['access_token'];

        $prod = GoodsReceipt::where('docnum', 'T000000002')->get()->first();

        $payload = [
            "id" => $prod->id
        ];

        $this->withHeaders(['Authorization'=>'Bearer '.$token,'Accept' => 'application/json'])->post('/api/goodsreceipt/delete/'.$prod->id, $payload)->assertStatus(200);
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
