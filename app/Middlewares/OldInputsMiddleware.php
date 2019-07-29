<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 16:05
 */

namespace App\Middlewares;

use App\Kernel\Abstracts\MiddlewareAbstract;
use Slim\Http\Request;
use Slim\Http\Response;

class OldInputsMiddleware extends MiddlewareAbstract
{
    public function __invoke(Request $request, Response $response, $next)
    {
        if (isset($_SESSION['old']))
            $this->getService('view')->getEnvironment()->addGlobal('old', $_SESSION['old']);
        $_SESSION['old'] = $request->getParams();
        $response = $next($request, $response);
        return $response;
    }
}