<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 25/08/2018
 * Time: 14:31
 */

namespace App\Services;

use App\Kernel\Interfaces\ServiceInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NotFoundHandlerService
 *
 * @package App\Services
 */
class NotFoundHandlerService implements ServiceInterface
{
    /**
     * @return mixed|string
     */
    public function name()
    {
        return 'notFoundHandler';
    }

    /**
     * @return \Closure|mixed
     */
    public function register()
    {
        return function () {
            return function (Request $request, Response $response) {
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(404)
                    ->write(json_encode(['code' => 404, 'message' => 'Ressources not found'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            };
        };
    }
}