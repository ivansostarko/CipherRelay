<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained()->cascadeOnDelete();
            $table->enum('direction', ['user','admin']);
            $table->longText('subject_cipher');
            $table->longText('subject_header');
            $table->longText('body_cipher');
            $table->longText('body_header');
            $table->string('file_path')->nullable();
            $table->string('file_original')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('messages');
    }
};
