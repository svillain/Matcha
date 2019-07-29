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

class SetupMiddleware extends MiddlewareAbstract
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $setup = env('SETUP', false);
        if ($setup) {
            if (isset($_SESSION['errors']))
                unset($_SESSION['errors']);
            if (isset($_SESSION['errors']))
                unset($_SESSION['user']);
            $path = base_path() . '/.env';

            if (file_exists($path)) {
                file_put_contents($path, str_replace(
                    'SETUP = true', 'SETUP = false', file_get_contents($path)
                ));
            }
            return $response->withRedirect($this->getService('router')->pathFor('setup'));
        }
        $response = $next($request, $response);
        return $response;
    }
}