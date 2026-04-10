<?php

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/app/Controllers/AuthController.php';

$controller = new AuthController();
$action = (string) ($_GET['action'] ?? 'index');
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($requestMethod === 'POST' && $action === 'login') {
    $controller->login();
    return;
}

match ($action) {
    'logout' => $controller->logout(),
    'dashboard' => $controller->dashboard(),
    default => $controller->index(),
};
