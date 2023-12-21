<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resttimes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('worktime_id')->unsigned()->index();
            $table->date('work_day');  
            $table->Time('rest_start_at');
            $table->Time('rest_end_at')->nullable();
            $table->time('rest_time')->nullable();            
            $table->timestamps();
            $table->foreign('worktime_id')->references('user_id')->on('worktimes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resttimes');
    }
}
