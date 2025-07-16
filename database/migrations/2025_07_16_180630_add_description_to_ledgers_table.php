<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->text('description')->nullable()->after('amount_paid'); // Replace 'amount_paid' as needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
