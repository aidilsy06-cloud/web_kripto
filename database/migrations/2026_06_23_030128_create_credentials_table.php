<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('platform_name_encrypted');
            $table->string('platform_name_iv');
            $table->text('platform_url_encrypted')->nullable();
            $table->string('platform_url_iv')->nullable();
            $table->text('username_encrypted');
            $table->string('username_iv');
            $table->text('password_encrypted');
            $table->string('password_iv');
            $table->text('notes_encrypted')->nullable();
            $table->string('notes_iv')->nullable();
            $table->string('strength');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
