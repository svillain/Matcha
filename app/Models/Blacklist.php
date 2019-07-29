<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 31/08/2018
 * Time: 11:32
 */

namespace App\Models;

use App\Kernel\Abstracts\ModelAbstract;

class Blacklist extends ModelAbstract
{
    protected $dbTable = 'blacklists';
    protected $dbFields = ['user_id', 'user_id_target', 'fake'];
    static $rules = [
    ];
}