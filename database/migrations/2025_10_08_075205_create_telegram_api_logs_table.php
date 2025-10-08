<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telegram_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('message_id');
            $table->unsignedBigInteger('payment_id');
            $table->foreign('payment_id')->references('id')->on('user_payments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_api_logs');
    }
};
