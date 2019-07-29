<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 16:06
 */

namespace App\Middlewares;

use App\Kernel\Abstracts\MiddlewareAbstract;
use App\Models\Blacklist;
use App\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

class ValideUserMiddleware extends MiddlewareAbstract
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $user = User::where('users.id', $_SESSION['user'])
            ->with('photos')
            ->with('interests')
            ->getOne();
        $uncompleted = [];
        /**
         * @var $route Route
         */
        $route = $request->getAttribute('route');
        $id = $route->getArgument('id');

        if (!$id) {
            $this->addMessage(ERROR, CAN_T_ACCESS);
            return $response->withRedirect($this->getService('router')->pathFor('home'));
        }
        $blacklisted = Blacklist::where('user_id', $id)
            ->where('user_id_target', $_SESSION['user'])
            ->getOne();

        if ($blacklisted) {
            $this->addMessage(ERROR, CAN_T_ACCESS);
            return $response->withRedirect($this->getService('router')->pathFor('home'));
        }

        if ($user->biography === null || empty($user->biography))
            $uncompleted['biography'] = 'Empty biography';
        if (empty($user->interests))
            $uncompleted['interests'] = 'No interests';
        if (empty($user->birthdate))
            $uncompleted['birthdate'] = 'Empty birthdate';
        if (!$user->photos)
            $uncompleted['photos_1'] = 'No photos upload';
        if (!$user->hasPhotoProfil())
            $uncompleted['photos_2'] = 'No profil photo set';

        if (count($uncompleted) > 0) {
            $this->addMessage(ERROR, CAN_T_ACCESS);
            return $response->withRedirect($this->getService('router')->pathFor('home'));
        }
        $response = $next($request, $response);
        return $response;
    }
}