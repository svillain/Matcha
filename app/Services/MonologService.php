<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 24/08/2018
 * Time: 17:26
 */

namespace App\Services;

use App\Kernel\Interfaces\ServiceInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

/**
 * Class MonologService
 * @package App\Services
 */
class MonologService implements ServiceInterface
{
    /**
     * @return mixed|string
     */
    public function name()
    {
        return 'logger';
    }

    /**
     * @return \Closure|mixed
     */
    public function register()
    {
        return function ($container) {
            $settings = $container->settings['logger'];
            $logger = new Logger($settings['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler($settings['path'], Logger::DEBUG));
            unset($container, $settings);
            return $logger;
        };
    }
}