<?php

namespace Composer\Route;

use Illuminate\Contracts\Routing\Registrar as Router;

class BackendRoute
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
        $this->router->group(['prefix' => 'backend/permission', 'middleware' => 'auth.backend', 'namespace' => 'App\Http\Controllers\Backend\Auth'], function () {
            $this->router->get('getSelect', 'PermissionController@getSelect');
        });
    }

    /**
     * 角色路由
     */
    public function forBackendRoleRoute()
    {
        $this->router->group(['prefix' => 'backend/role', 'middleware' => 'auth.backend', 'namespace' => 'App\Http\Controllers\Backend\Auth'], function () {
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
        $this->router->group(['prefix' => 'backend/user', 'middleware' => 'auth.backend', 'namespace' => 'App\Http\Controllers\Backend\Auth'], function () {
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
        $this->router->post('backend/auth/login', 'Composer\Application\Auth\BackendClient@login');
        $this->router->group(['prefix' => 'backend/auth', 'middleware' => 'auth.backend', 'namespace' => 'App\Http\Controllers\Backend\Auth'], function () {
            $this->router->get('currentUser', 'BackendController@currentUser');
            $this->router->get('public-token', 'BackendController@getPublicToken');
            $this->router->get('refresh', 'BackendController@refresh');
            $this->router->get('logout', 'BackendController@logout');
            $this->router->post('createUser', 'BackendController@create');
            $this->router->get('getPublicToken', 'BackendController@getPublicToken');
            $this->router->post('passwordReset', 'BackendController@passwordReset');
            $this->router->post('bindMail', 'BackendController@bindMail');
            $this->router->post('verifyBindMail', 'BackendController@verifyBindMail');
            $this->router->post('bindSms', 'BackendController@bindSms');
            $this->router->post('verifyBindSms', 'BackendController@verifyBindSms');
        });
    }
}
