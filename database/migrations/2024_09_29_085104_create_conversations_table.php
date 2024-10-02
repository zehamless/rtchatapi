<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->index();
            $table->boolean('is_group')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
