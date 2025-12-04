<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');              // "YouTube Premium Family"
            $table->string('billing_period');            // "monthly" | "yearly"
            $table->decimal('default_amount_eur', 8, 2); // full price per period
            $table->foreignId('owner_id')->constrained('people');
            $table->date('started_on');
            $table->date('ended_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
