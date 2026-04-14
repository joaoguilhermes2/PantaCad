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

if ($requestMethod === 'POST' && $action === 'update_first_access_password') {
    $controller->updateFirstAccessPassword();
    return;
}

if ($requestMethod === 'POST' && $action === 'update_profile') {
    $controller->updateProfile();
    return;
}

if ($requestMethod === 'POST' && $action === 'store_access') {
    $controller->storeAccess();
    return;
}

if ($requestMethod === 'POST' && $action === 'update_access') {
    $controller->updateAccess();
    return;
}

if ($requestMethod === 'POST' && $action === 'delete_access') {
    $controller->deleteAccess();
    return;
}

match ($action) {
    'accesses' => $controller->accesses(),
    'users' => $controller->users(),
    'first_access' => $controller->firstAccess(),
    'profile' => $controller->profile(),
    'logout' => $controller->logout(),
    'dashboard' => $controller->dashboard(),
    default => $controller->index(),
};
