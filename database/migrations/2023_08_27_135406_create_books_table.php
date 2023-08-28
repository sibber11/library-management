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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('genre_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('author')->nullable();
            $table->integer('price')->nullable();
            $table->smallInteger('available')->default(1);
            $table->text('description')->nullable();
            $table->date('published_at')->nullable();
            $table->timestamps();
        });
        //todo: add genre model and migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
