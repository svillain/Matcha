<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 30/08/2018
 * Time: 15:14
 */

namespace App\Controllers;

use App\Kernel\Abstracts\ControllerAbstract;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\User;
use App\Models\UserInterest;

class ChatController extends ControllerAbstract
{
    public function index()
    {
        $user = User::where('id', $_SESSION['user'])->getOne();
        if (empty($user->matchs())) {
            $this->addMessage(ERROR, CAN_T_ACCESS);
            return $this->redirect('home');
        }
        return $this->render('pages/chat.twig');
    }

    public function send()
    {
        $user_id_target = $this->input('target');
        $content = $this->input('content');
        // CHECK
        if (!isset($content) || empty($content) || strpos($content, "\n") !== false)
            return $this->writeJson(['error' => BAD_REQUEST], 400);
        if (strlen($content) > 255)
            return $this->writeJson(['error' => BAD_REQUEST], 400);
        if (!$user_id_target)
            return $this->writeJson(['error' => BAD_REQUEST], 400);
        if (!$this->getService('auth')->matchWith($user_id_target))
            return $this->writeJson(['error' => BAD_REQUEST], 400);

        $target = User::where('id', $user_id_target)->getOne();
        if (!$target)
            return $this->writeJson(['error' => BAD_REQUEST], 400);

        $message = new Message();
        $message->user_id = $_SESSION['user'];
        $message->user_id_target = $user_id_target;
        $message->content = htmlentities($content);
        $message->save();
        $current = User::where('id', $_SESSION['user'])->getOne();
        Notification::set('message', $user_id_target, $_SESSION['user'], $current->username . " speak with you!");
        return $this->writeJson(['message' => $message, 'success' => "Message sent to " . $target->username]);
    }

    public function update()
    {
        $uploadDir = '/upload';
        $user = User::where('id', $_SESSION['user'])->getOne();
        $user_matchs = $user->matchs();
        if (empty($user_matchs))
            return $this->writeJson([]);
        $groupMessages = [];

        foreach ($user_matchs as $key => $match) {
            $photo = Photo::where('user_id', $match->id)->where('profil', 1, 'AND')->getOne(['fileName']);
            $messages = Message::where('user_id', [$_SESSION['user'], $match->id], 'IN')
                ->where('user_id_target', [$_SESSION['user'], $match->id], 'IN')
                ->orderBy('created_at', 'ASC')
                ->get();
            $groupMessages[$match->id] = [
                'user' => $match,
                'photo' => $uploadDir . "/" . $photo->fileName,
                'messages' => $messages
            ];
            unset($photo);
        }
        return $this->writeJson($groupMessages);
    }
}