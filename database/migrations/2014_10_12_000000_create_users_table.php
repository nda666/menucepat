<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 40);
            $table->string('email')->unique();
            $table->string('password');
            $table->date('tgl_lahir');
            $table->string('kota_lahir', 40);
            $table->string('divisi', 40);
            $table->string('subdivisi', 40);
            $table->string('company', 40);
            $table->string('department', 40);
            $table->string('jabatan', 40);
            $table->string('lokasi', 40);
            $table->string('bagian', 40);
            $table->tinyInteger('sex');
            $table->text('alamat');
            $table->tinyInteger('blood');
            $table->bigInteger('family_id')->unsigned()->nullable();
            $table->string('whatsapp', 100)->nullable();
            $table->string('device_id', 100)->nullable();
            $table->string('notif_id', 100)->nullable();
            $table->string('nik', 100)->unique();
            $table->string('token')->nullable();
            $table->tinyInteger('lock')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
