<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('destination_country', 100);
            $table->date('start_date');
            $table->unsignedInteger('duration_days');
            $table->decimal('base_price', 10, 2);
            $table->timestamps();

            $table->index(['destination_country', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
