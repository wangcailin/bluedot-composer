<?php

namespace Composer;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
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
        $this->forWeChatRoute();
        $this->forWeChatRoute();
        $this->forAnalysisRoute();
    }

    /**
     * 权限用户路由
     */
    public function forBackendAuthRoute()
    {
        $this->router->group(['prefix' => 'backend'], function ($router) {
            $this->router->group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
                $this->router->post('login', 'BackendClient@login');
                $this->router->get('captcha', 'CaptchaClient@getCaptcha');

                $this->router->group(['middleware' => 'auth:backend'], function () {
                    $this->router->get('current-user', 'BackendClient@currentUser');
                    $this->router->get('logout', 'BackendClient@logout');

                    $this->router->post('bind-mail', 'BackendClient@bindMail');
                    $this->router->post('unbind-mail', 'BackendClient@unbindMail');
                    $this->router->get('unbind-mail', 'BackendClient@sendUnbindMail');

                    $this->router->post('bind-sms', 'BackendClient@bindSms');
                    $this->router->post('unbind-sms', 'BackendClient@unbindSms');
                    $this->router->get('unbind-sms', 'BackendClient@sendUnbindSms');

                    $this->router->post('password-reset', 'BackendClient@passwordReset');

                    $this->router->put('personal', 'BackendClient@updatePersonal');
                });

                $this->router->group(['prefix' => 'staff', 'middleware' => ['auth:backend', 'auth.admin']], function () {
                    $this->router->group(['prefix' => 'user'], function () {
                        $this->router->get('', 'UserClient@getList');
                        $this->router->get('{id}', 'UserClient@get');
                        $this->router->put('change/{id}', 'UserClient@change');
                        $this->router->put('{id}', 'UserClient@update');
                        $this->router->post('', 'UserClient@create');
                        $this->router->delete('{id}', 'UserClient@delete');
                    });
                    $this->router->group(['prefix' => 'role'], function () {
                        $this->router->get('', 'RoleClient@getList');
                        $this->router->get('select', 'RoleClient@getSelect');
                        $this->router->get('{id}', 'RoleClient@get');
                        $this->router->put('{id}', 'RoleClient@update');
                        $this->router->post('', 'RoleClient@create');
                        $this->router->delete('{id}', 'RoleClient@delete');
                    });
                    $this->router->group(['prefix' => 'permission'], function () {
                        $this->router->get('select', 'PermissionClient@getSelect');
                    });
                });
            });
        });
    }


    public function forWeChatRoute()
    {
        $this->router->group(
            ['prefix' => 'platform/wechat', 'namespace' => 'WeChat'],
            function () {
                $this->router->group(
                    ['prefix' => 'official-account', 'namespace' => 'OfficialAccount'],
                    function () {
                        $this->router->get('jssdk', 'JssdkClient@get');
                        $this->router->get('user/info', 'UserClient@getInfo');
                    }
                );


                $this->router->group(
                    ['prefix' => 'response', 'namespace' => 'Response'],
                    function () {
                        $this->router->get('{appid}', 'Client@response');
                        $this->router->post('{appid}', 'Client@response');
                    }
                );

                $this->router->group(
                    ['prefix' => 'auth'],
                    function () {
                        $this->router->get('oauth', 'AuthClient@oauth');
                        $this->router->get('auth', 'AuthClient@auth');
                    }
                );
            }
        );
    }

    public function forAnalysisRoute()
    {
        $this->router->group(
            ['prefix' => 'platform/analysis', 'namespace' => 'Analysis'],
            function () {
                $this->router->get('', 'MonitorClient@create');
            }
        );
    }

    public function forBackendTagRoute()
    {
        $this->router->group(
            ['prefix' => 'backend/tag', 'namespace' => 'Tag'],
            function () {
                $this->router->get('table-list', 'Client@getTableList');
                $this->router->get('tree-select', 'Client@getTreeSelectList');
                $this->router->post('', 'Client@create');
                $this->router->put('{id}', 'Client@update');

                $this->router->post('group', 'Client@createGroup');
                $this->router->put('group/{id}', 'Client@updateGroup');
            }
        );
    }
}
