<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productType_optionValues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productType_id')->nullable()->constrained('productTypes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('optionValue_id')->nullable()->constrained('optionValues')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('productType_optionValues');
    }
};
