<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 16:06
 */

namespace App\Middlewares;

use App\Kernel\Abstracts\MiddlewareAbstract;
use Slim\Http\Request;
use Slim\Http\Response;

class ValidationMiddleware extends MiddlewareAbstract
{
    public function __invoke(Request $request, Response $response, $next)
    {
        if (isset($_SESSION['errors']))
            $this->getService('view')->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
        unset($_SESSION['errors']);
        $response = $next($request, $response);
        return $response;
    }
}