<?php
/**
 * Created by PhpStorm.
 * User: Ashk
 * Date: 15/09/2018
 * Time: 18:42
 */

namespace App\Controllers;

use App\Kernel\Abstracts\ControllerAbstract;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\User;
use Slim\Http\UploadedFile;

class NotificationController extends ControllerAbstract
{

    public function update() {
        $notifications = Notification::where('user_id', $_SESSION['user'])->orderBy('updated_at', 'DESC')->get();
        return $this->writeJson($notifications);
    }

    public function read() {
        $id = $this->input('id');
        $notify = Notification::where('user_id', $_SESSION['user'])->where('id', $id, 'AND')->getOne();
        if ($notify)
            $notify->delete();
        else
            return $this->writeJson(ERROR);
        return $this->writeJson(SUCCESS);
    }

}