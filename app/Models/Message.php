<?php
/**
 * Created by PhpStorm.
 * User: Ashk
 * Date: 03/05/2019
 * Time: 15:28
 */

namespace App\Models;


use App\Kernel\Abstracts\ModelAbstract;

class Message extends ModelAbstract
{
    protected $dbTable = 'messages';
    protected $dbFields = ['user_id', 'user_id_target', 'content', 'seen'];
    static $rules = [
    ];

    protected $relations = [
        'user' => ["hasOne", User::class, 'user_id'],
        'target' => ["hasOne", User::class, 'user_id_target'],
    ];
}