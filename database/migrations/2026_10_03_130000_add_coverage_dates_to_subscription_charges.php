<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_charges', function (Blueprint $table) {
            $table->date('covered_from')->nullable();
            $table->date('covered_until')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_charges', function (Blueprint $table) {
            $table->dropColumn(['covered_from', 'covered_until']);
        });
    }
};
