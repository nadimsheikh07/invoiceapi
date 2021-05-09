<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict')->onUpdate('cascade');
            $table->text('comments');
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->foreignId('sale_id')->constrained('sales')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('restrict')->onUpdate('cascade');
            $table->integer('quantity');
            $table->decimal('price', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
        Schema::dropIfExists('sale_items');
    }
}