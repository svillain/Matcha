<?php


namespace App\Models;


use App\Kernel\Abstracts\ModelAbstract;

class Notification extends ModelAbstract
{
    protected $dbTable = 'notifications';
    public $dbFields = ['type', 'user_id', 'user_id_action', 'message', 'created_at', 'updated_at'];
    static $rules = [
    ];

    public static function set($type, $user_id, $user_id_action, $message) {
        $existing = Notification::where('type', $type)
            ->where('user_id', $user_id)
            ->where('user_id_action', $user_id_action)
            ->getOne();

        if (!$existing) {
            $notify = new Notification();
            $notify->type = $type;
            $notify->user_id = $user_id;
            $notify->user_id_action = $user_id_action;
            $notify->message = $message;
            $notify->save();
        }
        else
            $existing->update();
    }
}