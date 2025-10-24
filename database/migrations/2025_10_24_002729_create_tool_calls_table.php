<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tool_calls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->enum('tool', ['open_meteo','openai'])->index();
            $table->json('request')->nullable();
            $table->json('response')->nullable();
            $table->enum('status', ['ok','error'])->nullable();
            $table->integer('latency_ms')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('messages')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tool_calls');
    }
};


