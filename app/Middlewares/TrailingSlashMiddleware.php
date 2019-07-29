<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 20/08/2018
 * Time: 16:50
 */

namespace App\Middlewares;

use App\Kernel\Abstracts\MiddlewareAbstract;
use Slim\Http\Request;
use Slim\Http\Response;

class TrailingSlashMiddleware extends MiddlewareAbstract
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/') {
            $uri = $uri->withPath(substr($path, 0, -1));
            if ($request->getMethod() == 'GET')
                return $response->withRedirect((string)$uri, 301);
            else
                return $next($request->withUri($uri), $response);
        }
        return $next($request, $response);
    }
}
