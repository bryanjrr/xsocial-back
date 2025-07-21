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
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_category');
            $table->boolean('status')->default(true);
            $table->string('title');
            $table->text('content');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_language');
            $table->integer('views')->default(0);
            $table->integer('replies')->default(0);
            $table->timestamp('last_reply_at')->nullable();
            $table->string('image')->nullable();
            $table->string('type')->default('general');
            $table->string('tags')->nullable();
            $table->foreign('id_language')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('id_category')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
