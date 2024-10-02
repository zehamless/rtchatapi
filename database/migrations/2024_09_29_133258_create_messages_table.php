<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('conversation_id')->constrained('conversations');
            $table->text('content');
            $table->timestamp('sent_at');
            $table->timestamp('read_at')->nullable();
            $table->json('reader')->nullable();
        });
    }
};
