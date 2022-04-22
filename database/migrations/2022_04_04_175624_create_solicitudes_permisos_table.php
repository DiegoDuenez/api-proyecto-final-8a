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
        Schema::create('solicitudes_permisos', function (Blueprint $table) {
            $table->id();
            $table->integer('requesting_user');
            $table->string('solicitud');
            $table->string('requested_item')->nullable();
            $table->integer('status')->default(1);
            $table->string('code')->nullable();
            $table->string('tipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes_permisos');
    }
};
