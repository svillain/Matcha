<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 31/08/2018
 * Time: 11:32
 */

namespace App\Models;

use App\Kernel\Abstracts\ModelAbstract;

class Orientation extends ModelAbstract
{
    protected $dbTable = 'orientations';
    protected $dbFields = ['name'];
    static $rules = [
        'name' => ['required'],
    ];
}