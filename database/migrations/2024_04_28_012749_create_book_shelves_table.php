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
        Schema::create('book_shelves', function (Blueprint $table) {
            $table->id();
            $table->string('nameShelf');
            // $table->foreignId('bookId')->constrained('crud_books')->cascadeOnDelete();
            $table->foreignId('userId')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_shelves');
    }
};
