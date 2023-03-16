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
        Schema::create('cosplayers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('character');
            $table->string('anime');
            $table->unsignedInteger('number');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null')->onUpdate('set null');
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
        Schema::dropIfExists('cosplayers');
    }
};
