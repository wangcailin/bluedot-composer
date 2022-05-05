<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_user', function (Blueprint $table) {
            $table->id();
            $table->string('nickname', 100);
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->integer('is_admin')->default(0);
            $table->integer('is_active')->default(1);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('openid')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('loginfail_count')->default(0)->comment('失败次数');
            $table->timestamp('logintime')->nullable()->comment('登录时间');
            $table->timestamp('loginfail_time')->nullable()->comment('失败时间');
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
        Schema::dropIfExists('auth_user');
    }
}
