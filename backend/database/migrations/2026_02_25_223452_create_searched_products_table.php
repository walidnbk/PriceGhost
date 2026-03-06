<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // Assure-toi que cette ligne est là
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On remplace (Table $table) par (Blueprint $table)
        Schema::create('searched_products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amazon_price', 10, 2);
            $table->string('city')->default('Tangier'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('searched_products');
    }
};