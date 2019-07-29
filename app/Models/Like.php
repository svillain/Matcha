<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 31/08/2018
 * Time: 11:32
 */

namespace App\Models;

use App\Kernel\Abstracts\ModelAbstract;

class Like extends ModelAbstract
{
    protected $dbTable = 'likes';
    protected $dbFields = ['user_id', 'user_id_target'];
    static $rules = [
    ];

    protected $relations = [
        'user' => ["hasOne", User::class, 'user_id'],
        'target' => ["hasOne", User::class, 'user_id_target'],
    ];
}