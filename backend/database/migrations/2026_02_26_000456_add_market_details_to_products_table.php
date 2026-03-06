<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('searched_products', function (Blueprint $table) {
            $table->decimal('market_min', 10, 2)->nullable();
            $table->decimal('market_max', 10, 2)->nullable();
            $table->string('best_store')->nullable(); // Pour savoir qui est le moins cher !
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('searched_products', function (Blueprint $table) {
            $table->dropColumn('market_min');
            $table->dropColumn('market_max');
            $table->dropColumn('best_store');
        });
    }
};
