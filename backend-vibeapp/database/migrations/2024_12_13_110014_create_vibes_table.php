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
        Schema::create('vibes', function (Blueprint $table) {
            // $table->id();
            $table->uuid('id')->primary(); // UUID primary key
            $table->unsignedBigInteger('user_id'); // Relasi ke tabel users
            $table->unsignedBigInteger('category_id');
            $table->text('image_url');
            $table->text('comment')->nullable();
            $table->text('title');
            $table->text('desc');
            $table->text('path');
            $table->decimal('rating', 2, 1)->check('rating BETWEEN 0 AND 5');
            $table->boolean('blocked')->default(false);
            $table->timestamps();
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vibes');
    }
};
