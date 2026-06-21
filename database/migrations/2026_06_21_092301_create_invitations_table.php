<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('groom_name');
            $table->string('bride_name');
            $table->date('event_date');
            $table->time('akad_time')->nullable();
            $table->time('resepsi_time')->nullable();
            $table->string('location');
            $table->string('location_url')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
