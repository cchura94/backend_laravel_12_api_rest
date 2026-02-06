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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();

            $table->dateTime("fecha");
            $table->string("tipo_nota", 20);
            $table->decimal("impuestos", 12, 2)->nullable();
            $table->string("estado_nota", 50)->nullable();
            $table->string("observaciones")->nullable();

            // N:1
            $table->bigInteger("cliente_id")->unsigned();
            $table->bigInteger("user_id")->unsigned();

            $table->foreign("cliente_id")->references("id")->on("clientes");
            $table->foreign("user_id")->references("id")->on("users");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
