<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Tipe attachment: null = pesan teks biasa, 
            // 'gif' = GIF dari GIPHY, 'vibe' = share vibe (existing), 
            // 'image'/'voice'/'video' = future
            $table->string('attachment_type', 32)->nullable()->after('message_text');
            
            // Payload fleksibel per tipe (JSON column)
            // Untuk gif: { "id": "abc123", "url": "https://...", "preview": "...", "width": 480, "height": 360, "title": "..." }
            // Untuk vibe: { "vibe_id": "uuid", "title": "...", "image_url": "...", "rating": 4.5 }
            $table->json('attachment_payload')->nullable()->after('attachment_type');
            
            $table->index('attachment_type');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['attachment_type']);
            $table->dropColumn(['attachment_type', 'attachment_payload']);
        });
    }
};