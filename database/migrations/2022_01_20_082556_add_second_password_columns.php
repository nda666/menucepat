<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondPasswordColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('second_password', 255)->nullable()->after('password');
            $table->timestamp('second_password_expired')->nullable()->after('second_password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        (
            Schema::hasColumn('users', 'second_password') &&
            Schema::hasColumn('users', 'second_password_expired')
        ) && Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('second_password');
            $table->dropColumn('second_password_expired');
        });
    }
}
