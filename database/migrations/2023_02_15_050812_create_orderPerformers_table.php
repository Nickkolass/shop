<?php

use App\Models\OrderPerformer;
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
        Schema::create('order_performers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saler_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamp('dispatch_time');
            $table->jsonb('productTypes');
            $table->string('delivery');
            $table->unsignedMediumInteger('total_price');
            $table->unsignedTinyInteger('status')->default(OrderPerformer::STATUS_WAIT_PAYMENT)->index();
            $table->string('payout_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('order_performers');
    }
};
