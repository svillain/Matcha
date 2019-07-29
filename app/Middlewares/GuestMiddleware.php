<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 20/07/2018
 * Time: 15:50
 */

namespace App\Middlewares;

use App\Kernel\Abstracts\MiddlewareAbstract;
use Slim\Http\Request;
use Slim\Http\Response;

class GuestMiddleware extends MiddlewareAbstract
{
    public function __invoke(Request $request, Response $response, $next)
    {
        if ($this->getService('auth')->check()) {
            $this->addMessage(ERROR, CAN_T_ACCESS);
            return $response->withRedirect($this->getService('router')->pathFor('home'));
        }
        $response = $next($request, $response);
        return $response;
    }
}
