<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 30/08/2018
 * Time: 12:04
 */

namespace App\Services;

use App\Kernel\BaseServices\Mailer;
use App\Kernel\Interfaces\ServiceInterface;
use Slim\Container;

/**
 * Class MailerService
 *
 * @package App\Services
 */
class MailerService implements ServiceInterface
{
    /**
     * @return mixed|string
     */
    public function name()
    {
        return 'mailer';
    }

    /**
     * @return \Closure|mixed
     */
    public function register()
    {
        return function (Container $container) {
            return new Mailer($container);
        };
    }

}