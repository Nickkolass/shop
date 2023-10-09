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
        Schema::create('optionValues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_id')->constrained('options')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('value');
            $table->unique(['option_id', 'value']);
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
        Schema::dropIfExists('optionValues');
    }
};
