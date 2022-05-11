<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->boolean('state')->default(true);
            $table->timestamps();
        });

        Schema::create('tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('group_id');
            $table->string('name', 100);
            $table->string('remark')->nullable();
            $table->boolean('state')->default(true);
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('tag_group')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag');
        Schema::dropIfExists('tag_group');
    }
}
