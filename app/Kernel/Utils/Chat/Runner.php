<?php

namespace App\Kernel\Utils\Chat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Runner implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn) {
        echo 'TEST';
    }

    public function onMessage(ConnectionInterface $from, $msg) {
    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}