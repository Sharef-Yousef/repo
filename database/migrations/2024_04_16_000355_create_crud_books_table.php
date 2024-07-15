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
        Schema::create('crud_books', function (Blueprint $table) {
            $table->id();
            $table->string('nameBook');
            $table->string('nameAuth');
            $table->integer('numOfPage')->unsigned()->nullable();
            $table->text('aboutTheBook');
            $table->string('category');
            $table->string('bookImage')->nullable();
            $table->string('bookFile')->nullable();
            $table->Enum('bookType', ['text', 'audio']);
            $table->string('audioFile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crud_books');
    }
};
