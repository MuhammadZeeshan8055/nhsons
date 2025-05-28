<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceAndTotalToProductKeluarTable extends Migration
{
    public function up(): void
    {
        Schema::table('product_keluar', function (Blueprint $table) {
            if (!Schema::hasColumn('product_keluar', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
        
            if (!Schema::hasColumn('product_keluar', 'total')) {
                $table->decimal('total', 10, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_keluar', function (Blueprint $table) {
            $table->dropColumn(['price', 'total']);
        });
    }
}
