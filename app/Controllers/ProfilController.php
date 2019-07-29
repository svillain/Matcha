<?php
/**
 * Created by PhpStorm.
 * User: Ashk
 * Date: 28/09/2018
 * Time: 23:54
 */

namespace App\Controllers;

use App\Kernel\Abstracts\ControllerAbstract;
use App\Kernel\BaseServices\Auth;
use App\Models\Blacklist;
use App\Models\Like;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\User;
use App\Models\Visit;
use Slim\Http\Request;
use Slim\Http\UploadedFile;

class ProfilController extends ControllerAbstract
{
    public function index(Request $req)
    {
        $id = $req->getAttribute('id');

        if (!$id) {
            $this->addMessage(ERROR, USER_NOT_EXIST);
            return $this->redirect('home');
        }
        if ($blacklisted = Blacklist::where('user_id', $_SESSION['user'])->where('user_id_target', $id)->getOne()) {
            $this->addMessage(ERROR, USER_BLACKLISTED);
            return $this->redirect('home');
        }
        if ($blacklist = Blacklist::where('user_id', $id)->where('user_id_target', $_SESSION['user'])->getOne()) {
            $this->addMessage(ERROR, USER_BLACKLISTED_YOU);
            return $this->redirect('home');
        }
        if (!$user = User::where('users.id', $id)
            ->with('interests')
            ->with('sexe')
            ->with('orientation')
            ->with('photos')
            ->with('liked')
            ->with('visited')
            ->getOne()) {
            $this->addMessage(ERROR, USER_NOT_EXIST);
            return $this->redirect('home');
        }

        $uncompleted = [];

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
            $this->addMessage(ERROR, 'This user need to complete his profil !');
            return $this->redirect('home');
        }

        if ($user->id != $_SESSION['user']) {
            $visit = Visit::where('user_id_target', $id)->where('user_id', $_SESSION['user'])->getOne();
            if (!$visit) {
                $visit = new Visit();
                $visit->user_id = $_SESSION['user'];
                $visit->user_id_target = $id;
            }
            $visit->save();
            $current = User::where('id', $_SESSION['user'])->getOne();
            Notification::set('visit', $id, $_SESSION['user'], $current->username . " visit you");
        }

        if ($user->birthdate) {
            $bday = new \DateTime($user->birthdate);
            $today = new \Datetime(date('Y-m-d'));
            $diff = $today->diff($bday);
            $user->age = $diff->y;
        }
        return $this->render('pages/profil.twig', ['user' => $user]);
    }

    public function report(Request $req)
    {
        $fake = (!$this->input('fake')) ? false : true;
        $id = $req->getAttribute('id');

        if ($id === $_SESSION['user'] || !($user = User::where('id', $id)->getOne())) {
            $this->addMessage(ERROR, USER_NOT_EXIST);
            $this->redirect('home');
        }

        $blacklist = new Blacklist();
        $blacklist->user_id = $_SESSION['user'];
        $blacklist->user_id_target = $id;
        $blacklist->fake = $fake;

        $blacklist->save();
        // Delete messages
        $messages = Message::where('user_id', [$_SESSION['user'], $id], 'IN')
            ->where('user_id_target', [$_SESSION['user'], $id], 'IN')
            ->get();
        foreach ($messages as $m)
            $m->delete();
        // Delete like
        $likes = Like::where('user_id', [$_SESSION['user'], $id], 'IN')
            ->where('user_id_target', [$_SESSION['user'], $id], 'IN')
            ->get();
        foreach ($likes as $l)
            $l->delete();
        // Delete visits
        $visits = Visit::where('user_id', [$_SESSION['user'], $id], 'IN')
            ->where('user_id_target', [$_SESSION['user'], $id], 'IN')
            ->get();
        foreach ($visits as $l)
            $l->delete();

        $this->addMessage(SUCCESS, 'User ' . $user->username . ' has been blocked/reported !');
        return $this->redirect('home');
    }

    public function like(Request $req)
    {
        $user_id_target = $req->getAttribute('id');
        // check errors
        if ($user_id_target == $_SESSION['user']) {
            $this->addMessage(ERROR, CAN_T_LIKE);
            return $this->redirect('profil', ['id' => $user_id_target]);
        }
        if (!User::where('id', $user_id_target)->getOne()) {
            $this->addMessage(ERROR, CAN_T_LIKE);
            return $this->redirect('profil', ['id' => $user_id_target]);
        }
        // blacklisted
        if (Blacklist::where('user_id_target', $user_id_target)->where('user_id', $_SESSION['user'])->getOne()) {
            $this->addMessage(ERROR, CAN_T_LIKE);
            return $this->redirect('profil', ['id' => $user_id_target]);
        }
        // unlike

        $match = $this->getService('auth')->matchWith($user_id_target);

        if ($like = Like::where('user_id_target', $user_id_target)->where('user_id', $_SESSION['user'])->getOne()) {
            $current = User::where('id', $_SESSION['user'])->getOne();
            Notification::set('unlike', $user_id_target, $_SESSION['user'], $current->username . " stop to like you");
            if ($match) {
                Notification::set('unmatch', $user_id_target, $_SESSION['user'], $current->username . " stop to match with you");
                $messages = Message::where('user_id', [$_SESSION['user'], $user_id_target], 'IN')
                    ->where('user_id_target', [$_SESSION['user'], $user_id_target], 'IN')->get();
                foreach ($messages as $m)
                    $m->delete();
            }

            $like->delete();
            $this->addMessage(SUCCESS, ($match) ? UNMATCH : UNLIKE);
            return $this->redirect('profil', ['id' => $user_id_target]);
        }
        // like
        $like = new Like();
        $like->user_id = $_SESSION['user'];
        $like->user_id_target = $user_id_target;
        $like->save();

        $current = User::where('id', $_SESSION['user'])->getOne();
        Notification::set('like', $user_id_target, $_SESSION['user'], $current->username . " start to like you");

        $match = $this->getService('auth')->matchWith($user_id_target);
        if ($match) {
            Notification::set('match', $user_id_target, $_SESSION['user'], $current->username . " start to match with you");
        }
        $this->addMessage(SUCCESS, LIKE);
        return $this->redirect('profil', ['id' => $user_id_target]);
    }
}