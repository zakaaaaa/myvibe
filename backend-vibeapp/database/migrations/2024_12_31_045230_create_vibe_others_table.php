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
        Schema::create('vibe_others', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID primary key
            $table->unsignedBigInteger('category_other_id');
            $table->unsignedBigInteger('user_id');
            $table->text('image_url');
            $table->text('comment')->nullable();
            $table->text('title');
            $table->text('desc');
            $table->text('path');
            $table->decimal('rating', 2, 1)->check('rating BETWEEN 0 AND 5');
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
        Schema::dropIfExists('vibe_others');
    }
};
