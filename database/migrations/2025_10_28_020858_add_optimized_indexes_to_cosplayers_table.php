<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptimizedIndexesToCosplayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cosplayers', function (Blueprint $table) {
            // Add composite index for event_id and number for efficient lookups during bulk operations
            $table->index(['event_id', 'number'], 'cosplayers_event_number_idx');

            // Add separate index on number for cross-event searches
            $table->index('number', 'cosplayers_number_idx');
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
            // Drop indexes in reverse order
            $table->dropIndex('cosplayers_number_idx');
            $table->dropIndex('cosplayers_event_number_idx');
        });
    }
}
