<?php

use Composer\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_config', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32)->uniqid()->comment('类型');
            $table->jsonb('data');

            $table->timestamps();
            $table->comment('系统-配置表');
        });

        Schema::create('system_resource', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('mime_type', 64);
            $table->string('extension', 16);
            $table->integer('size');
            $table->string('url');
            $table->string('app_source')->nullable()->comment('应用来源');

            $table->timestamps();
            $table->comment('系统-资源表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_config');
        Schema::dropIfExists('system_resource');
    }
}
