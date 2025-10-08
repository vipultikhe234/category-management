<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_model', function (Blueprint $table) {
            $table->id();
            $table->string('category_name', 100)
                ->unique()
                ->comment('Name of the product category');
            $table->string('category_image', 255)
                ->nullable()
                ->comment('Image URL or path for the category');
            $table->unsignedBigInteger('parent_category')
                ->nullable()
                ->comment('Parent category of category');
            $table->enum('status', ['ON', 'OFF'])
                ->default('ON')
                ->comment('Status of category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_model');
    }
}
