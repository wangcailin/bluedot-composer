<?php

use Composer\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWechatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('wechat_material', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('auth_user_id');
            $table->string('name', 100);
            $table->string('remark')->nullable();
            $table->jsonb('data')->nullable();
            $table->string('appid', 32)->nullable();
            $table->boolean('state')->default(true);
            $table->integer('type')->comment('类型:1=图文,2=外链,3=文本');
            $table->timestamps();

            $table->comment('微信素材表');
        });

        Schema::create('wechat_qrcode', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('auth_user_id');
            $table->string('name', 100);
            $table->string('scene_str', 32);
            $table->string('ticket');
            $table->string('appid', 32);
            $table->string('url');

            $table->integer('reply_material_id')->comment('回推素材ID');
            $table->integer('reply_material_type')->comment('回推素材类型');
            $table->string('remark')->nullable();
            $table->jsonb('tag_ids')->nullable();
            $table->timestamps();

            $table->comment('微信二维码表');
        });

        Schema::create('wechat_reply', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('auth_user_id');
            $table->string('appid', 32);
            $table->string('match', 32)->nullable();
            $table->string('text', 100)->nullable();
            $table->integer('reply_material_id')->comment('回推素材ID');
            $table->integer('reply_material_type')->comment('回推素材类型');
            $table->string('remark')->nullable();
            $table->jsonb('tag_ids')->nullable();
            $table->string('type', 32)->comment('类型：keyword=关键字回复，msg=收到消息回复，subscribe=被关注回复');
            $table->timestamps();

            $table->comment('微信自动回复表');
        });

        Schema::create('wechat_openid', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('appid', 32);
            $table->string('openid', 32)->uniqid();
            $table->string('unionid', 32)->nullable();
            $table->integer('subscribe')->default(0);
            $table->integer('subscribe_time')->nullable();
            $table->string('subscribe_scene', 32)->nullable();
            $table->string('remark')->nullable();
            $table->string('qr_scene', 32)->nullable();
            $table->string('qr_scene_str', 32)->nullable();
            $table->string('nickname')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();

            $table->comment('微信用户表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_material');
        Schema::dropIfExists('wechat_qrcode');
        Schema::dropIfExists('wechat_reply');
        Schema::dropIfExists('wechat_openid');
    }
}
