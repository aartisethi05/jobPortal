<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Jobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->integer('provider_id');
            $table->string('company');
            $table->string('job_title');
            $table->text('skills'); 
            $table->text('description');
            $table->string('salary_range');
            $table->string('location');
            $table->string('experience');
            $table->text('education');
            $table->text('stream');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
