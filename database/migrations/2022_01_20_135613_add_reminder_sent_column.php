<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReminderSentColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->tinyInteger('reminder_sent')->default(0)->after('duty_off');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::hasColumn('schedules', 'reminder_sent') &&
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('reminder_sent');
            });
    }
}
