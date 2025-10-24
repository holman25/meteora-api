<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_id');
            $table->enum('role', ['system','user','assistant','tool'])->index();
            $table->text('content');
            $table->string('model')->nullable();
            $table->enum('status', ['pending','ok','error'])->default('pending');
            $table->string('error_code')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('chat_id')->references('id')->on('chats')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('messages');
    }
};


