<?php
/**
 * Created by PhpStorm.
 * User: adrie
 * Date: 24/08/2018
 * Time: 17:41
 */

require 'const.php';

return [
    'displayErrorDetails' => env('APP_DEBUG', false),
    'determineRouteBeforeAppMiddleware' => true,
    'addContentLengthHeader' => false,
    'base_url' => env('BASE_URL'),
    // 'routerCacheFile' => storage_path() . '/cache/routes.php',
    'database' => require 'database.php',
    'secrets' => require 'secrets.php',
    'services' => require 'services.php',
    'middlewares' => require 'middlewares.php',
    'logger' => require 'logger.php',
    'mailer' => require 'mailer.php',
    'upload_dir' => env('UPLOAD_DIR', 'upload')
];