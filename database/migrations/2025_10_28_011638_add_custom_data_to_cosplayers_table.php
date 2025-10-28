<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomDataToCosplayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cosplayers', function (Blueprint $table) {
            $table->json('custom_data')->nullable()->after('event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cosplayers', function (Blueprint $table) {
            $table->dropColumn('custom_data');
        });
    }
}
