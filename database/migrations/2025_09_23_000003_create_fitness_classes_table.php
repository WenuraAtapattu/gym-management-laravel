<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fitness_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('day_of_week', [
                'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
            ]);
            $table->integer('capacity')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index(['day_of_week', 'start_time']);
            $table->index('instructor_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fitness_classes');
    }
};
