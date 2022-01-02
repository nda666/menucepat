<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamp('check_clock')->nullable();
            $table->tinyInteger('clock_type');
            $table->string('latitude')->nullable();
            $table->string('longtitude')->nullable();
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->string('location_name')->nullable();
            $table->text('image')->nullable();
            $table->text('description')->nullable();
            $table->text('reason')->nullable();
            $table->tinyInteger('type')->default(0);
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('location_id')->references('id')->on('locations')
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
