<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_model', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 100)
                ->comment('name of the product');

            $table->string('product_image', 255)
                ->nullable()
                ->comment('Optional image path or URL of the product');
            $table->text('product_description')
                ->comment("Description of product");
            $table->unsignedBigInteger('category_id')
                ->comment('Foreign key referencing the product category');

            $table->enum('status', ['ON', 'OFF'])
                ->default('ON')
                ->comment('Product status: ON for active, OFF for inactive');

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('category_model')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_model');
    }
}
