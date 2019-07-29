<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 15:12
 */

namespace App\Models;

use App\Kernel\Abstracts\ModelAbstract;

/**
 * Class Photo
 *
 * @package App\Models
 */
class Photo extends ModelAbstract
{
    protected $dbTable = 'photos';
    protected $dbFields = ['user_id', 'fileName', 'profil', 'created_at', 'updated_at'];

    protected $relations = [
        'user' => ["hasOne", User::class, 'user_id']
    ];
}