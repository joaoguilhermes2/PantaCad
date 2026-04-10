<?php

declare(strict_types=1);

function view(string $view, array $data = []): void
{
    $viewFile = dirname(__DIR__) . '/app/Views/' . $view . '.php';

    if (!is_file($viewFile)) {
        throw new RuntimeException('View nao encontrada: ' . $view);
    }

    extract($data, EXTR_SKIP);
    require $viewFile;
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function flash(string $key, mixed $value = null): mixed
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $flash = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    if (isset($_SESSION['_flash']) && $_SESSION['_flash'] === []) {
        unset($_SESSION['_flash']);
    }

    return $flash;
}

