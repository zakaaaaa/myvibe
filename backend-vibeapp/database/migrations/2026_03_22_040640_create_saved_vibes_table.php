<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('saved_vibes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('vibe_id');
            $table->timestamps();
            $table->unique(['user_id', 'vibe_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_vibes');
    }
};
