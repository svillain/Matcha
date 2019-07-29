<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 30/08/2018
 * Time: 17:25
 */

namespace App\Controllers;

use App\Kernel\Abstracts\ControllerAbstract;
use App\Kernel\BaseServices\Database;
use App\Models\Interest;
use App\Models\Orientation;
use App\Models\User;
use App\Models\UserInterest;

class SearchController extends ControllerAbstract
{
    private $_sql = '';
    private $_binds = [];
    private static $_previous = null; // '_sql' => null, '_binds' => null
    /**
     * @var array $this ->_request
     */
    private $_request;

    public function reqValidation()
    {
        $expected = ['orderBy', 'limits', 'filters'];
        $expected_filters = ['age', 'location', 'popularity', 'interests'];
        $expected_orders = ['age_old', 'age_young', 'location', 'popularity', 'interests'];
        // is an array ?
        if (!is_array($this->_request)) return false;
        // a good array ?
        if (count($this->_request) != count($expected)) return false;
        // a real good array ??
        if (count(array_diff(array_values($expected), array_keys($this->_request))) !== 0) return false;
        // the filters key is an array ?
        if (!is_array($this->_request['filters'])) return false;
        // the good filters ???
        if (count(array_diff(array_values($expected_filters), array_keys($this->_request['filters']))) !== 0) return false;
        // orderBy valid ?
        if (!in_array($this->_request['orderBy'], $expected_orders)) return false;

        foreach ($this->_request['filters'] as $k => $v) {
            if (!is_array($v))
                return false;
            switch ($k) {
                case 'interests':
                    $tmp = array_filter($v);
                    if (count($tmp) === 0)
                        break;
                    if (Interest::where('id', $tmp, 'IN')->count() != count($tmp))
                        return false;
                    break;
                default:
                    if (count($v) !== 2) return false;
                    if (count($v) !== count(array_filter($v, 'is_numeric'))) return false;
                    if ($v[0] < 0 || $v[0] > 100 || $v[0] > $v[1]) return false;
                    if ($v[1] < 0 || $v[1] > 100) return false;
                    break;
            }
        }
        return true;
    }

    public function search()
    {
        $this->_request = $this->input('request');

        if (!$this->_request)
            return $this->writeJson(BAD_REQUEST, 403);

        if (!$this->reqValidation())
            return $this->writeJson(BAD_REQUEST, 403);

        $user = $this->getService('auth')->user();

        $sexes = [];
        $orientations = [];

        // Hetero
        if ($user->orientation_id === 2) {
            $orientations = [1, 2];
            if ($user->sexe_id === 1)
                $sexes = [2];
            else
                $sexes = [1];
        }
        // Homo
        if ($user->orientation_id === 3) {
            $orientations = [1, 3];
            $sexes = [$user->sexe_id];
        }
        // Bisexuel
        if ($user->orientation_id === 1) {
            $revert_sexe = ($user->sexe_id === 1 ? 2 : 1);
            $current_sexe = $user->sexe_id;
            $sexes_orientations = " AND ((u.sexe_id = $current_sexe AND u.orientation_id != 2) OR (u.sexe_id = $revert_sexe AND u.orientation_id != 3)) ";
        }
        else {
            $sexes_orientations = " AND u.sexe_id IN (" . implode(',', $sexes) . ") AND u.orientation_id IN (" . implode(',', $orientations) . ") ";
        }
        // distance in KM between current and users
        $distance = "
            (6371
            * acos(cos(radians(?))
            * cos(radians(u.latitude))
            * cos(radians(u.longitude)
            - radians(?))
            + sin(radians(?))
            * sin(radians(u.latitude)))) AS distance
        ";

        $score = "
            ((SELECT count(*) FROM visits WHERE user_id_target = u.id) + (SELECT count(*) FROM likes WHERE user_id_target = u.id) - (SELECT count(*) FROM blacklists WHERE user_id_target = u.id)) AS score
        ";

        $user_interests = Database::getInstance()->rawQueryValue('SELECT interest_id FROM users_interests WHERE user_id = ' . $_SESSION['user']);
        if (!$user_interests || count($user_interests) === 0) {
            $user_interests = [];
            $user_interests[] = 100000;
        }
        $c_interests = "
            (SELECT count(*) FROM users_interests WHERE users_interests.user_id = u.id AND users_interests.interest_id IN (" . implode(',', $user_interests) . ")) as c_interests
        ";

        $this->_sql = "SELECT DISTINCT(u.id), u.first_name, (SELECT name FROM orientations WHERE id = u.orientation_id) as orientation, (SELECT name FROM sexes WHERE id = u.sexe_id) as sexe," . $distance . "," . $c_interests . "," . $score . ", date_format(u.birthdate, '%Y/%m/%e') as birthdate_format  FROM users u LEFT JOIN users_interests it ON it.user_id = u.id WHERE u.id != ?";
        $this->_sql .= $sexes_orientations;
        $this->_sql .= " AND u.id NOT IN (SELECT b.user_id_target FROM blacklists b WHERE (b.user_id = ? AND u.id = b.user_id_target)) ";
        // Why this shit bug at school ?????????????????????????????!!!!!!!! OK It's useless so comment it!
        //$this->_sql .= " AND u.id NOT IN (SELECT b2.user_id_target FROM blacklists b2 WHERE b2.user_id_target = u.id AND b2.fake = 1 HAVING count(b2.user_id_target) >= 10) ";
        $this->_sql .= " AND u.id NOT IN (SELECT b3.user_id FROM blacklists b3 WHERE (b3.user_id = u.id AND b3.user_id_target = ?)) ";
        $this->_binds[] = $user->latitude;
        $this->_binds[] = $user->longitude;
        $this->_binds[] = $user->latitude;
        $this->_binds[] = $_SESSION['user'];
        $this->_binds[] = $_SESSION['user'];
        $this->_binds[] = $_SESSION['user'];

        $this->setFilters();
        $this->setOrderBy();

        $this->_sql .= " LIMIT ?, 28";
        $this->_binds[] = $this->_request['limits'][0];

        $raw_query = Database::getInstance()->rawQuery($this->_sql, $this->_binds);
        if ($raw_query === null || count($raw_query) === 0 || empty($raw_query))
            return $this->writeJson(['result' => [], 'raw' => [], 'sql' => $this->_sql]);
 ;
        // TODO: delete invalid user (cf : condition)
        $query = array_column($raw_query, 'id');
        $order = implode(',', $query);

        $users = User::with('photos')
            ->with('interests')
            ->with('sexe')
            ->with('orientation')
            ->where('users.id', $query, 'IN')
            ->orderBy("FIELD(users.id, $order)", "ASC")
            ->get();
        if (!$users) $users = [];

        foreach ($users as $ku => $u) {
            $completed = true;
            if ($u->biography === null || empty($u->biography))
                $completed = false;
            if (empty($u->interests))
                $completed = false;
            if (empty($u->birthdate))
                $completed = false;
            if (!$u->photos)
                $completed = false;
            if (!$u->hasPhotoProfil())
                $completed = false;
            if (!$completed) {
                unset($raw_query[$ku]);
                unset($users[$ku]);
                $users = array_values($users);
                $raw_query = array_values($raw_query);
            }
        }

        return $this->writeJson(['result' => $users, 'raw' => $raw_query, 'sql' => $this->_sql]);
    }

    private function setFilters()
    {
        // INTERESTS
        $this->_request['filters']['interest'][] = '';
        $interests = array_filter($this->_request['filters']['interests']);
        if (count($interests) !== 0) {
            $this->_sql .= " AND it.interest_id IN (" . implode(',', $interests) . ") ";
        }
        // AGE
        $min_age = $this->_request['filters']['age'][0];
        $max_age = $this->_request['filters']['age'][1];

        if (!($min_age === '0' && $max_age === '0')) {
            $min_age_date = date('Y-m-d', strtotime($min_age . ' years ago'));
            $max_age_date = date('Y-m-d', strtotime($max_age . ' years ago'));
            if ($min_age === '0') {
                $this->_sql .= " AND birthdate > ? ";
                $this->_binds[] = $max_age_date;
            } else {
                $this->_sql .= " AND birthdate BETWEEN ? AND ? ";
                $this->_binds[] = $max_age_date;
                $this->_binds[] = $min_age_date;
            }
        }
        // DISTANCE
        $min_location = $this->_request['filters']['location'][0];
        $max_location = $this->_request['filters']['location'][1];
        if (!($min_location === '0' && $max_location === '0')) {
            $this->_sql .= " HAVING distance BETWEEN ? AND ? ";
            $this->_binds[] = $min_location;
            $this->_binds[] = $max_location;
        }

        // POPULARITY
        $min_score = $this->_request['filters']['popularity'][0];
        $max_score = $this->_request['filters']['popularity'][1];
        if (!($min_score === '0' && $max_score === '0')) {
            if (!($min_location === '0' && $max_location === '0'))
                $this->_sql .= " AND score BETWEEN ? AND ? ";
            else
                $this->_sql .= " HAVING score BETWEEN ? AND ? ";
            $this->_binds[] = $min_score;
            $this->_binds[] = $max_score;
        }
    }

    private function setOrderBy()
    {
        switch ($this->_request['orderBy']) {
            // plus populaire au moins populaire
            case 'popularity':
                $this->_sql .= " ORDER BY score DESC";
                break;
            // plus vieux au plus jeune
            case 'age_old':
                $this->_sql .= "ORDER BY birthdate_format";
                break;
            case 'age_young':
                $this->_sql .= "ORDER BY birthdate_format DESC";
                break;
            // plus proche au plus loin
            case 'location';
                $this->_sql .= " ORDER BY distance ASC";
                break;
            case 'interests':
                $query = Database::getInstance()->rawQueryValue("SELECT interest_id FROM users_interests WHERE user_id = ?", [$_SESSION['user']]);
                if ($query === null || count($query) === 0 || empty($query))
                    $query = [];
                $this->_sql .= "ORDER BY c_interests DESC";
                break;
            default:
                break;
        }
    }
}