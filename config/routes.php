<?php

use App\Controllers\AuthController;
use App\Controllers\ProdutoController;
use App\Middleware\AuthMiddleware;
use Laminas\Diactoros\Response\RedirectResponse;
use League\Route\Router;

$routes = new Router();
$routes->map('GET', '/', function() {
    return new RedirectResponse('/produtos');
});

$routes->map('GET', '/cadastro', [AuthController::class, 'cadastroForm']);
$routes->map('POST', '/cadastro', [AuthController::class, 'cadastro']);

$routes->map('GET', '/login', [AuthController::class, 'loginForm']);
$routes->map('POST', '/login', [AuthController::class, 'login']);
$routes->map('GET', '/logout', [AuthController::class, 'logout']);

$routes->map('GET', '/lembrar-senha', [AuthController::class, 'lembrarSenhaForm']);
$routes->map('POST', '/lembrar-senha', [AuthController::class, 'lembrarSenha']);

$routes->map('GET', '/redefinir-senha', [AuthController::class, 'redefinirSenhaForm']);
$routes->map('POST', '/redefinir-senha', [AuthController::class, 'redefinirSenha']);

$routes->group('/produtos', function ($routes) {
    $routes->map('GET', '/', [ProdutoController::class, 'index']);
    $routes->map('GET', '/{id:number}', [ProdutoController::class, 'show']);
    $routes->map('GET', '/cadastrar', [ProdutoController::class, 'createForm']);
    $routes->map('POST', '/cadastrar', [ProdutoController::class, 'store']);
    $routes->map('POST', '/excluir', [ProdutoController::class, 'delete']);
    $routes->map('GET', '/editar/{id:number}', [ProdutoController::class, 'editForm']);
    $routes->map('POST', '/editar/{id:number}', [ProdutoController::class, 'update']);
})->middleware(new AuthMiddleware());
