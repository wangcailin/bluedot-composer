<?php

namespace Composer\Route;

use Illuminate\Contracts\Routing\Registrar as Router;

class BackendAuthRoute
{
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->forBackendAuthRoute();
        $this->forBackendPermissionRoute();
        $this->forBackendRoleRoute();
        $this->forBackendUserRoute();
    }

    /**
     * 权限路由
     */
    public function forBackendPermissionRoute()
    {
        $this->router->group(['prefix' => 'backend/permission', 'middleware' => 'auth.backend', 'namespace' => '\Composer\Http\Controllers\Backend\Auth'], function () {
            $this->router->get('getSelect', 'PermissionController@getSelect');
        });
    }

    /**
     * 角色路由
     */
    public function forBackendRoleRoute()
    {
        $this->router->group(['prefix' => 'backend/role', 'middleware' => 'auth.backend', 'namespace' => '\Composer\Http\Controllers\Backend\Auth'], function () {
            $this->router->get('get/{id}', 'RoleController@get');
            $this->router->post('create', 'RoleController@create');
            $this->router->put('update/{id}', 'RoleController@update');
            $this->router->get('getSelect', 'RoleController@getSelect');
        });
    }

    /**
     * 权限用户路由
     */
    public function forBackendUserRoute()
    {
        $this->router->group(['prefix' => 'backend/user', 'middleware' => 'auth.backend', 'namespace' => '\Composer\Http\Controllers\Backend\Auth'], function () {
            $this->router->get('get/{id}', 'UserController@get');
            $this->router->post('create', 'UserController@create');
            $this->router->get('change/{id}', 'UserController@change');
        });
    }

    /**
     * 登录
     * @return void
     */
    public function forBackendAuthRoute()
    {
        $this->router->post('backend/auth/login', '\Composer\Http\Controllers\Backend\Auth\AuthController@login');
        $this->router->group(['prefix' => 'backend/auth', 'middleware' => 'auth.backend', 'namespace' => '\Composer\Http\Controllers\Backend\Auth'], function () {
            $this->router->get('currentUser', 'AuthController@currentUser');
            $this->router->get('public-token', 'AuthController@getPublicToken');
            $this->router->get('refresh', 'AuthController@refresh');
            $this->router->get('logout', 'AuthController@logout');
            $this->router->post('createUser', 'AuthController@create');
            $this->router->get('getPublicToken', 'AuthController@getPublicToken');
            $this->router->post('passwordReset', 'AuthController@passwordReset');
            $this->router->post('bindMail', 'AuthController@bindMail');
            $this->router->post('verifyBindMail', 'AuthController@verifyBindMail');
            $this->router->post('bindSms', 'AuthController@bindSms');
            $this->router->post('verifyBindSms', 'AuthController@verifyBindSms');
        });
    }
}
