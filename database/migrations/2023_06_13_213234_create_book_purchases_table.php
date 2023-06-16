<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('book_id')->unsigned();
            $table->integer('copies')->unsigned()->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_purchases');
    }
};
