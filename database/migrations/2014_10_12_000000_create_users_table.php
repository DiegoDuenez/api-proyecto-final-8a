<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username_usuario');
            $table->string('nombre_usuario');
            $table->string('apellidos_usuario');
            $table->string('email_usuario')->unique();
            $table->string('numero_usuario')->unique();
            $table->boolean('email_verified')->default(false);
            $table->string('email_code_usuario')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password_usuario');
            $table->integer('rol_id')->unsigned();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
