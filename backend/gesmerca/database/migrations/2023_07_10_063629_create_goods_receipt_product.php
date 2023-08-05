<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_receipt_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idgoodsreceipt');
            $table->unsignedBigInteger('idproduct');
            $table->foreign('idgoodsreceipt')->references('id')->on('goods_receipts')->onDelete('cascade');
            $table->foreign('idproduct')->references('id')->on('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->double('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_products');
    }
};
