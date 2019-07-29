<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 20/07/2018
 * Time: 11:27
 */

namespace App\Kernel\Abstracts;

use Slim\Container as Container;

/**
 * Class UtilAbstract
 *
 * @package App\Kernel\Abstracts
 */
abstract class BaseServiceAbstract
{
    /**
     * @var Container $container
     */
    protected $container;

    /**
     * UtilAbstract constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $service
     * @return mixed
     */
    protected function getService($service)
    {
        return $this->container->{$service};
    }
}
