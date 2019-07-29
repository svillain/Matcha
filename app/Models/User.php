<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 27/08/2018
 * Time: 15:12
 */

namespace App\Models;

use App\Kernel\Abstracts\ModelAbstract;
use App\Kernel\BaseServices\Database;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * Class User
 *
 * @package App\Models
 */
class User extends ModelAbstract
{
    // User
    // $user->
    protected $dbTable = 'users';
    protected $dbFields = ['username', 'email', 'password', 'first_name', 'last_name', 'biography', 'sexe_id', 'orientation_id', 'longitude', 'latitude', 'ip', 'reset', 'confirmation','disconnected_at', 'created_at', 'updated_at'];
    static $rules = [
        'username' => ['required', ['lengthBetween', 4, 50]],
        'email' => ['required', 'email', ['lengthMax', 255]],
        'password' => ['required', ['lengthBetween', 8, 24], ['regex', '/^.*(?=.{8,})((?=.*[!@#$%^&*()\-_=+{};:,<.>]){1})(?=.*\d)((?=.*[a-z]){1})((?=.*[A-Z]){1}).*$/']], // TODO: reinforced
        'first_name' => ['required', ['lengthBetween', 2, 24] , 'alpha'],
        'last_name' => ['required', ['lengthBetween', 6, 24], 'alpha'],
        'biography' => ['optional', ['lengthMax', 255]],
        'sexe_id' => ['sexeValide', 'integer'],
        'orientation_id' => ['optional', 'integer'],
        'birthdate' => ['optional', 'birthdateValide'],
        'longitude' => ['optional', 'numeric'],
        'latitude' => ['optional', 'numeric'],
        'reset' => ['optional']
    ];
    protected $relations = [
        'sexe' => ["hasOne", Sexe::class, 'sexe_id'],
        'orientation' => ["hasOne", Orientation::class, 'orientation_id'],
        'interests' => ["belongstomany", UserInterest::class, 'user_id', Interest::class, 'interest_id'],
        'photos' => ["hasMany", Photo::class, 'user_id'],
        'liked' => ["hasMany", Like::class, 'user_id_target'],
        'likes' => ["hasMany", Like::class, 'user_id'],
        'visited' => ["hasMany", Visit::class, 'user_id_target'],
        'visit' => ["hasMany", Visit::class, 'user_id'],
    ];

    public function matchs($rawQuery=false) {
        // DU CODE BIEN MOCHE ICI HUHU => relation un peu compliquée
        $query = Database::getInstance()->rawQueryValue("SELECT l1.user_id_target FROM likes l1 INNER JOIN likes l2 ON l1.user_id = l2.user_id_target AND l1.user_id_target = l2.user_id AND l1.user_id = ?", [$this->id]);
        if ($query === null || empty($query))
            return [];
        if ($rawQuery)
            return $query;
        return User::where('id', $query, 'IN')->get();
    }

    public function score() {
        $nlike = Like::where('user_id_target', $this->id)->count();
        $nview = Visit::where('user_id_target', $this->id)->count();
        $nblack = Blacklist::where('user_id_target', $this->id)->count();

        $s = $nlike + $nview - $nblack;
        return ($s > 0) ? $s : 0;
    }

    public function hasPhotoProfil() {
        $profil = false;
        if (!$this->photos)
            return false;
        foreach ($this->photos as $key => $photo) {
            if ($photo->profil === 1) {
                $profil = true;
                break;
            }
        }
        return $profil;
    }
}