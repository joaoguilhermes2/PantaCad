<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

loadEnv(dirname(__DIR__) . '/.env');

function database(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = env('DB_HOST');
    $port = env('DB_PORT', '5432');
    $name = env('DB_NAME');
    $user = env('DB_USER');
    $password = env('DB_PASSWORD');

    if (!$host || !$name || !$user || $password === null) {
        throw new RuntimeException('As variaveis de ambiente do banco de dados nao foram configuradas corretamente.');
    }

    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $name);

    $connection = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $connection;
}
