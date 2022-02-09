<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThumbsAttendanceImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->text('image_thumbnail')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::hasColumn('attendances', 'image_thumbnail') &&
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('image_thumbnail');
            });
    }
}