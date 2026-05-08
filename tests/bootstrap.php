<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Charger .env.test
if (file_exists(dirname(__DIR__).'/.env.test')) {
    (new Dotenv())->load(dirname(__DIR__).'/.env.test');
}

// S'assurer que KERNEL_CLASS est défini
if (!$_SERVER['KERNEL_CLASS'] ?? !getenv('KERNEL_CLASS')) {
    putenv('KERNEL_CLASS=App\Kernel');
    $_SERVER['KERNEL_CLASS'] = $_ENV['KERNEL_CLASS'] = 'App\Kernel';
}