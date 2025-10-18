<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('passcode_hash', 255);
            $table->enum('status', ['new_from_user','add_more','closed'])->default('new_from_user');
            $table->longText('key_cipher'); // base64
            $table->longText('key_header'); // base64
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('threads');
    }
};
