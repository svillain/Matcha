<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 24/08/2018
 * Time: 17:32
 */

use Dotenv\Dotenv;
// Load Dotenv //
if (file_exists(base_path() . '/.env')) {
    $_dotenv = new Dotenv(base_path());
    $_dotenv->overload();

    unset($_dotenv);
}