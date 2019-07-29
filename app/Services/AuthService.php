<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 25/08/2018
 * Time: 17:58
 */

namespace App\Services;

use App\Kernel\BaseServices\Auth;
use App\Kernel\Interfaces\ServiceInterface;
use Slim\Container;

/**
 * Class AuthService
 *
 * @package App\Services
 */
class AuthService implements ServiceInterface
{
    /**
     * @return mixed|string
     */
    public function name()
    {
        return 'auth';
    }

    /**
     * @return \Closure|Auth
     */
    public function register()
    {
        return function (Container $container) {
            return new Auth($container);
        };
    }

}