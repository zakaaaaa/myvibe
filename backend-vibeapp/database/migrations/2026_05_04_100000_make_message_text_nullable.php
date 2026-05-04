<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pakai raw SQL biar tidak butuh doctrine/dbal
        DB::statement('ALTER TABLE messages MODIFY message_text TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE messages MODIFY message_text TEXT NOT NULL');
    }
};
