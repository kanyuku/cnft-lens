<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_traits', function (Blueprint $table) {
            $table->id();
            $table->string('asset_id')->index();
            $table->string('trait_type');
            $table->string('value');
            $table->timestamps();

            $table->foreign('asset_id')->references('asset_id')->on('assets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_traits');
    }
};
