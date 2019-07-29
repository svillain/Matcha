<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 24/08/2018
 * Time: 17:20
 */

namespace App\Services;

use App\Kernel\BaseServices\Database;
use App\Kernel\Interfaces\ServiceInterface;
use Illuminate\Database\Capsule\Manager;

/**
 * Class DatabaseService
 * @package App\Services
 */
class DatabaseService implements ServiceInterface
{
    /**
     * @return mixed|string
     */
    public function name()
    {
        return 'db';
    }

    /**
     * @return \Closure|mixed
     */

    public function register()
    {
        return function ($container) {
            return new Database($container);
        };
    }
}