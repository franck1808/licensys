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
        //
        Schema::create('licensekeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_app_id')->constrained()->cascadeOnDelete();
            $table->text('key');
            $table->date('start_at');
            $table->date('end_at');
            $table->boolean('isActive')->nullable();
            $table->boolean('renew')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('licensekeys');
    }
};
