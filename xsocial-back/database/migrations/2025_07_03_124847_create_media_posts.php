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
        Schema::create('media_posts', function (Blueprint $table) {
            $table->id();
            $table->text('file_url');
            $table->unsignedBigInteger('media_id');
            $table->unsignedBigInteger('content_type');
            $table->string("media_type");
            $table->timestamps();
            $table->foreign('media_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('content_type')->references('id')->on('content_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_posts');
    }
};
