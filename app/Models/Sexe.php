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
 * Class Sexe
 *
 * @package App\Models
 */
class Sexe extends ModelAbstract
{
    protected $dbTable = 'sexes';
    protected $dbFields = ['name'];
    protected $timestamps = false;

    protected $relations = [
        'users' => ["hasMany", User::class, 'sexe_id']
    ];
}