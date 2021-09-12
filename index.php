<?php

declare(strict_types=1);

namespace App;

use App\Exception\AppException;
use App\Exception\ConfigurationException;
use Throwable;

require_once("src/Utils/debug.php");
require_once("src/Controller.php");
require_once("src/Exception/AppException.php");

$configuration = require_once("config/config.php");

$request = [
    'get' => $_GET,
    'post' => $_POST
];

try {
    Controller::initConfiguration($configuration);
    (new Controller($request))->run();
} catch (ConfigurationException $e) {
    echo '<h1>' . $e->getMessage() . '</h1>';
    echo '<h1>Skontaktuj się z administratorem</h1>';
}catch (AppException $e) {
    echo '<h1>' . $e->getMessage() . '</h1>';
} catch (Throwable $e) {
    echo '<h1>Wystąpił błąd</h1>';
}
