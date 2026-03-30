<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique(); // Serial number or generated value
            $table->string('name'); // Asset name
            $table->string('asset_tag')->unique(); // New asset_tag column
            $table->unsignedBigInteger('asset_model_id')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('asset_model_id')->references('id')->on('asset_models')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
