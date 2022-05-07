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
        Schema::create('users_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id')->comment("Id do usuário que está enviando o dinheiro");
            $table->unsignedBigInteger('recipient_id')->comment("Id do usuário que irá receber o dinheiro");
            $table->decimal('sender_balance')->comment("Valor do saldo o usuário pagador no momento da transferência");
            $table->decimal('recipient_balance')->comment("Valor do saldo o usuário recebedor no momento da transferência");
            $table->decimal('value_transaction')->comment("Valor que será transferido");
            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('recipient_id')->references('id')->on('users');
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
        Schema::dropIfExists('users_transactions');
    }
};
