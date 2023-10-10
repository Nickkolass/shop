<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('productType_optionValues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productType_id')->constrained('productTypes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('optionValue_id')->constrained('optionValues')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['productType_id', 'optionValue_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('productType_optionValues');
    }
};
