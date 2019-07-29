<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 24/08/2018
 * Time: 16:35
 */

namespace App\Kernel\Interfaces;

/**
 * Interface ServiceInterface
 *
 * @package App\Kernel\Interfaces
 */
interface ServiceInterface
{
    /**
     * Service register name
     *
     * @return mixed
     */
    public function name();

    /**
     * Register new service on dependency container
     *
     * @return mixed
     */
    public function register();
}