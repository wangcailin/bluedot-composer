<?php

namespace Composer\Application\WeChat\Qrcode\Login;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class Workman extends Command
{
    /**
     * 命令名称及签名
     *
     * @var string
     */
    protected $signature = 'workman:qrcode-login {action} {--d}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = 'Start a Workerman server.';

    /**
     * 创建命令
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        global $argv;
        $action = $this->argument('action');

        $argv[0] = 'artisan workman:qrcode-login';
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : ''; //必须是一个-，上面定义命令两个--，后台启动用两个--
        $this->start();
    }

    private function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }

    private function startBusinessWorker()
    {
        $worker = new BusinessWorker();
        $worker->name = 'QrcodeLoginBusinessWorker';
        $worker->count = 1;
        $worker->registerAddress = '127.0.0.1:1237';
        $worker->eventHandler = WebSocket::class;
    }

    private function startGateWay()
    {
        $gateway = new Gateway("websocket://0.0.0.0:2347");
        $gateway->name = 'QrcodeLoginGateway';
        $gateway->count = 1;
        $gateway->lanIp = '127.0.0.1';
        $gateway->startPort = 2300;
        $gateway->pingInterval = 30;
        $gateway->pingNotResponseLimit = 0;
        $gateway->pingData = '{"type":"ping"}';
        $gateway->registerAddress = '127.0.0.1:1237';
    }

    private function startRegister()
    {
        new Register('text://0.0.0.0:1237');
    }
}
