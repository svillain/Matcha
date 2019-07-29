<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 24/08/2018
 * Time: 17:42
 */

return [
    \App\Middlewares\TrailingSlashMiddleware::class,
    //\App\Middlewares\SetupMiddleware::class,
    \App\Middlewares\OldInputsMiddleware::class,
    \App\Middlewares\ValidationMiddleware::class,
];