<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalysisMonitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analysis_monitor', function (Blueprint $table) {
            $table->integer('type')->comment('类型：1=H5,2=微信公众号,3=小程序');
            $table->integer('user_id')->nullable();
            $table->string('user_ip', 100)->nullable();
            $table->string('user_network', 100)->nullable();
            $table->jsonb('user_agent')->nullable();

            $table->string('unionid', 100)->nullable();
            $table->string('openid', 100)->nullable();

            $table->string('page_title')->nullable();
            $table->text('page_url')->nullable();
            $table->jsonb('page_param')->nullable();
            $table->string('page_event_key')->nullable();
            $table->string('page_event_type', 100)->nullable();
            $table->text('page_referer_url')->nullable();

            $table->jsonb('keywords')->nullable();
            $table->string('wechat_user_name')->nullable();
            $table->string('wechat_appid', 100)->nullable();
            $table->string('wechat_event_key')->nullable();
            $table->string('wechat_event_msg')->nullable();
            $table->string('wechat_event_type')->nullable();
            $table->jsonb('wechat_event_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analysis_monitor');
    }
}
