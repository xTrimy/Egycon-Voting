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
        Schema::create('poll_data_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_data_id')->constrained('poll_data')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('poll_line_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('value');
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
        Schema::dropIfExists('poll_data_lines');
    }
};
