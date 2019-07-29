<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 20/08/2018
 * Time: 14:22
 */

namespace App\Kernel\Abstracts;

use App\Kernel\BaseServices\Auth;
use App\Kernel\BaseServices\Database;
use App\Kernel\BaseServices\Mailer;
use App\Kernel\Interfaces\ControllerInterface;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Container as Container;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class ControllerAbstract
 *
 * @package App\Kernel\Abstracts
 */
abstract class ControllerAbstract implements ControllerInterface
{
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * ControllerAbstract constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $this->getService('logger');
        unset($container);
    }

    /**
     * @param $file
     * @param array $datas
     *
     * @return ResponseInterface
     */
    public function render($file, $datas = [])
    {
        return $this->getService('view')->render($this->getResponse(), $file, $datas);
    }

    /**
     * @param $name
     * @param string $anchor
     *
     * @return ResponseInterface
     */
    public function redirect($name, $params = '')
    {
        $response = $this->getResponse()->withStatus(302);
        if (is_array($params))
            return $response->withHeader('Location', $this->container->router->pathFor($name, $params));
        return $response->withHeader('Location', $this->container->router->pathFor($name) . $params);
    }

    /**
     * @param array|string $datas
     * @param int $code
     *
     * @return ResponseInterface
     */
    public function writeJson($datas, $code = 200)
    {
        return $this->getResponse()
            ->withHeader("Content-Type", "application/json")
            ->withStatus($code)
            ->write(json_encode($datas));
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function input($name)
    {
        return $this->getRequest()->getParam($name);
    }

    /**
     * @return array|null
     */
    public function inputs()
    {
        return $this->getRequest()->getParams();
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

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return ServerRequestInterface
     */
    protected function getRequest()
    {
        return $this->container->request;
    }

    /**
     * @return ResponseInterface
     */
    protected function getResponse()
    {
        return $this->container->response;
    }

    /**
     * @param $service
     *
     * @return Auth|Mailer|Database|Messages|Twig
     */
    protected function getService($service)
    {
        return $this->container->{$service};
    }
}
