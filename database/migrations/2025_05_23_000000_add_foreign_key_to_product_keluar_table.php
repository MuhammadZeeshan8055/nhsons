<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToProductKeluarTable extends Migration
{
    public function up(): void
    {
        Schema::table('product_keluar', function (Blueprint $table) {
            if (!Schema::hasColumn('product_keluar', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_keluar', function (Blueprint $table) {
            if (Schema::hasColumn('product_keluar', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
}
