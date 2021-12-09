<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Families extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('hubungan');
            $table->string('nama');
            $table->tinyInteger('sex');
            $table->string('tempat_lahir');
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('family_id')->references('id')->on('families')
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('families');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
