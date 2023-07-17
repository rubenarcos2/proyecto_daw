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
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idsupplier');
            $table->unsignedBigInteger('iduser');
            $table->foreign('idsupplier')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('iduser')->references('id')->on('users')->onDelete('cascade');
            $table->date('date')->useCurrent();
            $table->time('time')->useCurrent();
            $table->string('docnum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
