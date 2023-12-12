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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('partno')->nullable();
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            $table->string('madein')->nullable();
            $table->string('size')->nullable();
            $table->string('uom')->nullable();
/*             $table->unsignedBigInteger('coy');
            $table->unsignedBigInteger('cat'); */
            $table->float('pprice')->nullable();
            $table->float('price')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
