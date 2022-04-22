<?php

namespace Composer\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use phpseclib\Crypt\RSA as LegacyRSA;
use phpseclib3\Crypt\RSA;
use Illuminate\Support\Arr;

class CryptGenerateCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'crypt:generate';

    protected $description = 'Set the application crypt key';

    public function handle()
    {
        $this->handleAes();
        $this->handleRsa();

        $this->info('Application Crypt key set successfully.');
    }

    protected function handleAes()
    {
        $iv = bin2hex(random_bytes(8));
        if (!$this->setKeyInEnvironmentFile('AES_IV', $iv)) {
            return;
        }
        $key = bin2hex(random_bytes(16));
        if (!$this->setKeyInEnvironmentFile('AES_KEY', $key)) {
            return;
        }
    }

    protected function handleRsa()
    {
        $publicKey = '';
        $privateKey = '';
        if (class_exists(LegacyRSA::class)) {
            $keys = (new LegacyRSA())->createKey(4096);
            $publicKey = Arr::get($keys, 'publickey');
            $privateKey = Arr::get($keys, 'privatekey');
        } else {
            $key = RSA::createKey(4096);
            $publicKey = (string) $key->getPublicKey();
            $privateKey = (string) $key;
        }
        if (!$this->setKeyInEnvironmentFile('RSA_PRIVATE_KEY', $privateKey)) {
            return;
        }
        if (!$this->setKeyInEnvironmentFile('RSA_PUBLIC_KEY', $publicKey)) {
            return;
        }
    }

    protected function setKeyInEnvironmentFile($key, $value)
    {
        $this->writeNewEnvironmentFileWith($key, $value);

        return true;
    }

    protected function writeNewEnvironmentFileWith($key, $value)
    {
        $environmentFilePath = $this->laravel->environmentFilePath();
        $before = file_get_contents($environmentFilePath);
        file_put_contents($environmentFilePath, str_replace(
            "{$key}=",
            "{$key}=\"{$value}\"",
            $before
        ));
    }
}
