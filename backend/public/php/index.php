<?php

$APP_DIRECTORY = __DIR__ . "/../../app/";

require __DIR__ . '/../../vendor/autoload.php';

spl_autoload_extensions(".php");
spl_autoload_register(function ($class) {
    global $APP_DIRECTORY;
    $class = str_replace("\\", "/", $class);
    $file = $APP_DIRECTORY . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Logging\Logger;
use Http\Response\HttpResponse;
use Exceptions\Interface\UserVisibleException;
use Render\JSONRenderer;
use Settings\Settings;

function getRequestDirectory()
{
    $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
    return $uri;
}

try {
    date_default_timezone_set(Settings::env("TIMEZONE"));
    $logger = Logger::getInstance();
    $logger->logRequest();
    $directory = getRequestDirectory();
    $routes = include($APP_DIRECTORY . 'Routing/routes.php');
    $renderer = null;
    foreach ($routes as $uriPattern => $route) {
        $middleware = $route['middleware'];
        $controller = $route['controller'];
        if (preg_match($uriPattern, $directory)) {
            $renderer = $middleware->handle($controller);
        }
    }
    if (is_null($renderer)) {
        $param = [
            "result" => "failure",
            'title' => '404 Not Found',
            'headline' => '404 Not Found',
            'message' => 'There is no content associated with the specified URL.'
        ];
        $renderer = new JSONRenderer(404, $param);
    }
    $httpResponse = new HttpResponse($renderer);
} catch (UserVisibleException $e) {
    $param = [
        "result" => "failure",
        'title' => '400 Bad Request',
        'headline' => '400 Bad Request',
        'message' => $e->displayErrorMessage()
    ];
    $httpResponse = new HttpResponse(new JSONRenderer(400, $param));
    $logger->logError($e);
} catch (Throwable $e) {
    $param = [
        "result" => "failure",
        'title' => '500 Internal Server Error',
        'headline' => '500 Internal Server Error',
        'message' => 'Internal error, please contact the admin.'
    ];
    $httpResponse = new HttpResponse(new JSONRenderer(500, $param));
    $logger->logError($e);
} finally {
    $httpResponse->send();
    $logger->logResponse($httpResponse);
}
