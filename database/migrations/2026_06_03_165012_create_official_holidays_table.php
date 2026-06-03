<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('official_holidays', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->date('starts_on')->index();
            $table->date('ends_on')->index();
            $table->unsignedInteger('days')->default(1);
            $table->unsignedSmallInteger('year')->nullable()->index();
            $table->boolean('is_recurring')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_holidays');
    }
};