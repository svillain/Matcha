<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 01:26
 */

namespace App\Services;

use App\Kernel\Interfaces\ServiceInterface;
use Slim\Csrf\Guard;

/**
 * Class CsrfService
 *
 * @package App\Services
 */
class CsrfService implements ServiceInterface
{
    /**
     * @return mixed|string
     */
    public function name()
    {
        return 'csrf';
    }

    /**
     * @return \Closure|Guard
     */
    public function register()
    {
        return function () {
            $guard = new Guard();
            $guard->setPersistentTokenMode(true);
            return $guard;
        };
    }
}