<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 24/08/2018
 * Time: 16:35
 */

namespace App\Kernel;

use App\Kernel\Interfaces\ServiceInterface;
use App\Kernel\Utils\Validation\Rules;
use Slim\Container;
use Slim\App as BaseApp;

/**
 * Class App
 *
 * @package App\Kernel
 */
class App extends BaseApp
{
    public function loadValidatorCustomRules() {
        $container = $this->getContainer();
        $rules = new Rules($container);
        $rules->load();
    }
    /**
     * Register new services on dependency container
     */
    public function registerServices()
    {
        /**
         * @var $container Container
         */
        $container = $this->getContainer();
        $services = $container->settings['services'];
        if (is_array($services) && !empty($services)) {
            foreach ($services as $service) {
                /**
                 * @var $instance ServiceInterface
                 */
                $instance = new $service();
                $container[$instance->name()] = $instance->register();
                unset($instance);
            }
        }
        unset($container, $services, $service);
    }

    /**
     * Register App Middlewares
     */
    public function registerAppMiddlewares()
    {
        /**
         * @var $container Container
         */
        $container = $this->getContainer();
        $middlewares = $container->settings['middlewares'];
        if (is_array($middlewares) && !empty($middlewares)) {
            foreach ($middlewares as $middleware) {
                $this->add($middleware);
            }
        }
        unset($container, $middlewares, $middleware);
    }
}