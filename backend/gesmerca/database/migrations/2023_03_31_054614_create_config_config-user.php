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
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('value');
            $table->string('title');
            $table->string('description');
            $table->string('domain');
            $table->timestamps();
        });

        Schema::create('config_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idconfig');
            $table->unsignedBigInteger('iduser');
            $table->string('value')->nullable();
            $table->string('description')->nullable();
            $table->foreign('idconfig')->references('id')->on('configs')->onDelete('cascade');
            $table->foreign('iduser')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_users');
        Schema::dropIfExists('configs');        
    }
};

