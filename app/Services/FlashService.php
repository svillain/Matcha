<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 01:23
 */

namespace App\Services;

use App\Kernel\Interfaces\ServiceInterface;
use Slim\Flash\Messages;

/**
 * Class FlashService
 *
 * @package App\Services
 */
class FlashService implements ServiceInterface
{
    /**
     * @return mixed|string
     */
    public function name()
    {
        return 'flash';
    }

    /**
     * @return \Closure|Messages
     */
    public function register()
    {
        return function () {
            return new Messages;
        };
    }
}