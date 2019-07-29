<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 20/08/2018
 * Time: 16:49
 */

namespace App\Kernel\Abstracts;

use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerInterface;
use Slim\Container as Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class MiddlewareAbstract
 *
 * @package App\Kernel\Abstracts
 */
abstract class MiddlewareAbstract
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * MiddlewareAbstract constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        unset($container);
    }

    /**
     * Middleware
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    abstract public function __invoke(Request $request, Response $response, $next);

    /**
     * Get Slim Container
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Get Service From Container
     *
     * @param string $service
     *
     * @return mixed
     */
    protected function getService($service)
    {
        return $this->container->{$service};
    }

    /**
     * @param string $type
     * @param string $content
     *
     * @return mixed
     */
    public function addMessage($type, $content)
    {
        return $this->getService('flash')->addMessage($type, $content);
    }
}
