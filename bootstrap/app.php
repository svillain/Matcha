<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 24/08/2018
 * Time: 17:31
 */

use \App\Kernel\App;
session_start();

require 'kernel.php';

$app = new App(['settings' => require config_path() . '/app.php']);

$app->registerServices();
$app->registerAppMiddlewares();
$app->loadValidatorCustomRules();


require app_path() . '/Routes.php';

try {
    $app->getContainer()->get('db');
} catch (\Psr\Container\NotFoundExceptionInterface $e) {
} catch (\Psr\Container\ContainerExceptionInterface $e) {
    return ($e->getMessage());
}

try {
    $app->run();
} catch (\Slim\Exception\MethodNotAllowedException $e) {
} catch (\Slim\Exception\NotFoundException $e) {
} catch (Exception $e) {
    dd($e);
}
