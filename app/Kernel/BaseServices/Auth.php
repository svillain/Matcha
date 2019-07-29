<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 25/08/2018
 * Time: 17:29
 */

namespace App\Kernel\BaseServices;

use App\Kernel\Abstracts\BaseServiceAbstract;
use App\Models\Blacklist;
use App\Models\Like;
use App\Models\User;

class Auth extends BaseServiceAbstract
{
    public function user()
    {
        if (isset($_SESSION['user']))
            return User::byId($_SESSION['user']);
        return false;
    }

    public function hasLike($id)
    {
        $like = Like::where('user_id', $_SESSION['user'])
            ->where('user_id_target', $id)
            ->getOne();

        return !is_null($like);
    }

    public function isLikeBy($id)
    {
        $like = Like::where('user_id', $id)
            ->where('user_id_target', $_SESSION['user'])
            ->getOne();
        return !is_null($like);
    }

    public function hasBlocked($id)
    {
        $blocked = Blacklist::where('user_id', $_SESSION['user'])
            ->where('user_id_target', $id)
            ->getOne();
        return !is_null($blocked);
    }

    public function matchWith($id)
    {
        $match = Like::where('user_id', [$_SESSION['user'], $id], 'IN')
            ->where('user_id_target', [$_SESSION['user'], $id], 'IN')
            ->getValue('count(*)');
        return ($match === 2);
    }

    public function check()
    {
        $isset = isset($_SESSION['user']);

        if ($isset) {
            $user = $this->user();
            $user->online = 1;
            $user->save();
        }
        return $isset;
    }

    public function attempt($username, $password)
    {
        if (!$user = User::where('username', $username)->getOne())
            return false;
        if ($this->equalsHash($user->password, $password)) {
            $_SESSION['user'] = $user->id;
            $user->online = 1;
            $user->save();
            return true;
        }
        return false;
    }

    public function logout()
    {
        $user = $this->user();
        $user->online = 0;
        $user->disconnected_at = date("Y-m-d H:i:s");
        $user->save();
        unset($_SESSION['user']);
    }

    public function hashPassword($password)
    {
        $salt = $this->getService('settings')['secrets']['passwordSalt'];
        return hash('whirlpool', $salt . $password);
    }

    public function equalsHash($userPassword, $password)
    {
        return hash_equals($userPassword, $this->hashPassword($password));
    }
}