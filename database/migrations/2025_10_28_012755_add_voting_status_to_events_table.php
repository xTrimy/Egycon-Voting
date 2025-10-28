<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVotingStatusToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('judge_voting_enabled')->default(true)->after('name');
            $table->timestamp('voting_starts_at')->nullable()->after('judge_voting_enabled');
            $table->timestamp('voting_ends_at')->nullable()->after('voting_starts_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['judge_voting_enabled', 'voting_starts_at', 'voting_ends_at']);
        });
    }
}
