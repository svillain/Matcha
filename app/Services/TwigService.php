<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 01:14
 */

namespace App\Services;

use App\Kernel\Interfaces\ServiceInterface;
use Slim\Container;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

/**
 * Class TwigService
 *
 * @package App\Services
 */
class TwigService implements ServiceInterface
{
    /**
     * @return mixed|string
     */
    public function name()
    {
        return 'view';
    }

    /**
     * @return \Closure|Twig
     */
    public function register()
    {
        return function (Container $container) {
            $view = new \Slim\Views\Twig(app_path() . '/Views', [
                'debug' => env('APP_DEBUG', false)
            ]);
            $view->addExtension(
                new TwigExtension(
                    $container->router,
                    $container->request->getUri()
                )
            );
            $view->addExtension(new \Twig_Extension_Debug());
            $view->getEnvironment()->addGlobal('settings', $container->settings);
            $view->getEnvironment()->addGlobal('flash', $container->flash);
            $view->getEnvironment()->addGlobal('auth', $container->auth);
            $view->getEnvironment()->addGlobal('queries', $container->request->getQueryParams());
            return $view;
        };
    }

}