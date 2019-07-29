<?php
use Ratchet\Server\IoServer;
use App\Kernel\Utils\Chat\Runner;

require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
    new Runner(),
    5000
);

$server->run();