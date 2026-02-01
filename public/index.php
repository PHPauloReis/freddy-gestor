<?php

session_start();

use App\Controllers\BaseController;
use App\Exceptions\ResourceNotFoundException;
use App\Support\LoggerFactory;
use Dotenv\Dotenv;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Http\Exception\NotFoundException;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$logger = LoggerFactory::create();

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

require __DIR__ . '/../config/routes.php';

try {
    $response = $routes->dispatch($request);
} catch (NotFoundException | ResourceNotFoundException $e) {
    $controller = new BaseController();
    $response = new HtmlResponse(
        $controller->render('errors/404.twig'),
        404
    );
} catch (Throwable $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);

    $controller = new BaseController();
    $response = new HtmlResponse(
        $controller->render('errors/500.twig', ['message' => $e->getMessage()]),
        500
    );
}

$sapiEmitter = new SapiEmitter();
$sapiEmitter->emit($response);
