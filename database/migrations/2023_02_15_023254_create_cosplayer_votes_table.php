<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cosplayer_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cosplayer_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('vote',false,true)->default(0);
            $table->text('comment')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cosplayer_votes');
    }
};
