<?php

use Carbon\Carbon;
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
        Schema::create('check_in_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('pretix_id')->nullable();
            $table->dateTime('start_time')->default(Carbon::minValue());
            $table->dateTime('end_time')->default(Carbon::minValue());
            $table->enum('type', ['TICKET', 'MERCH', 'SPECIAL'])->default('TICKET');
            $table->json('pretix_product_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_in_lists');
    }
};
