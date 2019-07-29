<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 17/08/2018
 * Time: 20:55
 */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\SettingsController;
use App\Controllers\PhotosController;
use App\Controllers\ChatController;
use App\Controllers\ProfilController;
use App\Controllers\NotificationController;
use App\Controllers\SearchController;

/************************** INDEX ************************/
$app->get('/', HomeController::class . ':index')->setName('home');
/************************** NEED TO BE GUEST ************************/
$app->group('', function () use ($app) {
    $app->post('/register', AuthController::class . ':register')->setName('register');
    $app->post('/connexion', AuthController::class . ':connexion')->setName('connexion');
    $app->get('/forgot', AuthController::class . ':forgot')->setName('forgot');
    $app->post('/forgot', AuthController::class . ':forgot')->setName('forgot');
    $app->get('/reset/{id}', AuthController::class . ':reset')->setName('reset');
    $app->post('/reset/{id}', AuthController::class . ':reset')->setName('reset');
    $app->get('/confirmation/{id}', AuthController::class . ':confirmation')->setName('confirmation');
    $app->get('/setup', HomeController::class . ':setup')->setName('setup');
})->add(new \App\Middlewares\GuestMiddleware($app->getContainer()));



/************************** NEED TO BE CONNECTED ************************/
$app->group('', function () use ($app) {
    /** ME : CURRENT SESSION ROUTES **/
    $app->group('/me', function () use ($app) {
        /** SEARCH **/
        $app->post('/search', SearchController::class . ':search')->setName('search');
        /** PHOTOS **/
        $app->get('/photos', PhotosController::class . ':index')->setName('photos');
        $app->post('/photos', PhotosController::class . ':save');
        $app->post('/photos/{id}/delete', PhotosController::class . ':delete')->setName('photos/delete');
        $app->post('/photos/{id}/profil', PhotosController::class . ':setProfil')->setName('photos/profil');
        /** SETTINGS **/
        $app->get('/settings', SettingsController::class . ':index')->setName('settings');
        $app->post('/settings', SettingsController::class . ':save');
        /** SETTINGS PASSWORD **/
        $app->post('/settings/password', SettingsController::class . ':changePassword')->setName('settings/password');
        /** CHAT **/
        $app->get('/chat', ChatController::class . ':index')->setName('chat');
        $app->post('/chat/send', ChatController::class . ':send')->setName('chat/send');
        $app->post('/chat/load', ChatController::class . ':load')->setName('chat/load');
        $app->post('/chat/update', ChatController::class . ':update')->setName('chat/update');
        /** NOTIFICATIONS **/
        $app->post('/notification/update', NotificationController::class . ':update')->setName('notification/update');
        $app->post('/notification/read', NotificationController::class . ':read')->setName('notification/read');
    });
    $app->group('', function () use ($app) {
    /** PROFIL **/
        $app->get('/profil/{id}', ProfilController::class . ':index')->setName('profil');
        /** REPORT **/
        $app->post('/report/{id}', ProfilController::class . ':report')->setName('report');
        /** LIKE **/
        $app->post('/like/{id}', ProfilController::class . ':like')->setName('like');
        /** LOGOUT **/
    })->add(new \App\Middlewares\ValideUserMiddleware($app->getContainer()));
    $app->get('/logout', AuthController::class . ':logout')->setName('logout');
})->add(new \App\Middlewares\AuthMiddleware($app->getContainer()));