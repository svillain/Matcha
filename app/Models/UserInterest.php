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
class UserInterest extends ModelAbstract
{
    protected $dbTable = 'users_interests';
    protected $dbFields = ['user_id', 'interest_id'];
    protected $primaryKey = 'user_id';
    protected $timestamps = false;
}