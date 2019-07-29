<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 28/08/2018
 * Time: 15:58
 */

namespace App\Kernel\Utils\Validation;

use App\Models\Sexe;
use App\Models\User;
use Psr\Container\ContainerInterface;

/**
 * Class Rules
 *
 * @package App\Kernel\Utils\Validation
 */
class Rules
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var
     */
    protected static $_ruleMessages;

    /**
     * Rules constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        static::$_ruleMessages = include 'messages.php';
        $this->container = $container;
        unset($container);
    }

    /**
     *
     */
    public function load()
    {
        $this->emailAvailable();
        $this->usernameAvailable();
        $this->sexeValide();
        $this->passwordVerification();
        $this->passwordUser();
        $this->birthdateValide();
    }

    /**
     *
     */
    private function emailAvailable()
    {
        Validation::addRule(__FUNCTION__, function ($field, $value, array $params, array $fields) {
            if (isset($_SESSION['user']))
                return !User::where('email', $value)->where('id', $_SESSION['user'], '!=')->getOne();
            else
                return !User::where('email', $value)->getOne();
        }, self::$_ruleMessages['emailAvailable']);
    }

    /**
     *
     */
    private function usernameAvailable()
    {
        Validation::addRule(__FUNCTION__, function ($field, $value, array $params, array $fields) {
            return !User::where('username', $value)->getOne();
        }, self::$_ruleMessages['usernameAvailable']);
    }

    /**
     *
     */
    private function sexeValide()
    {
        Validation::addRule(__FUNCTION__, function ($field, $value, array $params, array $fields) {
            if (!isset($value) || empty($value))
                return false;
            return Sexe::where('id', $value)->getOne();
        }, self::$_ruleMessages['sexeValide']);
    }

    /**
     *
     */
    private function passwordVerification()
    {
        Validation::addRule(__FUNCTION__, function ($field, $value, array $params, array $fields) {
            if (!isset($value) || empty($value))
                return false;
            if ($value !== $params[0])
                return false;
            return true;
        }, self::$_ruleMessages['passwordVerification']);
    }

    /**
     *
     */
    private function passwordUser()
    {
        Validation::addRule(__FUNCTION__, function ($field, $value, array $params, array $fields) {
            if (!isset($value) || empty($value))
                return false;
            return $this->container->get('auth')->equalsHash(User::where('id', $_SESSION['user'])->getOne()->password, $value);
        }, self::$_ruleMessages['passwordUser']);
    }

    /**
     *
     */
    private function birthdateValide()
    {
        Validation::addRule(__FUNCTION__, function ($field, $value, array $params, array $fields) {
            if (!isset($value) || empty($value))
                return true;
            $today_time = strtotime('now');
            $minimum_time =  strtotime("-16 years");
            $maximun_time = strtotime("-80 years");
            $birthdate_time = strtotime($value);
            if ($birthdate_time > $today_time || $maximun_time > $birthdate_time || $minimum_time < $birthdate_time)
                return false;
            return true;
        }, self::$_ruleMessages['birthdateValide']);
    }
}