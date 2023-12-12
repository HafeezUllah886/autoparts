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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor')->nullable();
            $table->string('walking')->nullable();
            $table->unsignedBigInteger('paidFrom')->nullable();
            $table->timestamp('date', $precision = 0);
            $table->text('desc')->nullable();
            $table->string('isPaid');
            $table->unsignedBigInteger('amount')->nullable();
            $table->unsignedBigInteger('ref');
            $table->foreign('vendor')->references('id')->on('accounts');
            $table->foreign('paidFrom')->references('id')->on('accounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
