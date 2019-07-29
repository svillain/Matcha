<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 20/07/2018
 * Time: 15:48
 */
namespace App\Middlewares;

use App\Kernel\Abstracts\MiddlewareAbstract;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthMiddleware extends MiddlewareAbstract
{
    public function __invoke(Request $request, Response $response, $next)
    {
        if(!$this->getService('auth')->check()) {
            $this->addMessage(ERROR, NEED_TO_BE_CONNECTED);
            return $response->withRedirect($this->getService('router')->pathFor('home'));
        }
        $response = $next($request, $response);
        return $response;
    }
}
