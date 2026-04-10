<?php

declare(strict_types=1);

final class User
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByCredentials(string $email, string $senha): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nome, email
             FROM usuarios
             WHERE LOWER(email) = LOWER(:email)
               AND ativo = TRUE
               AND senha_hash = crypt(:senha, senha_hash)
             LIMIT 1'
        );

        $stmt->execute([
            'email' => $email,
            'senha' => $senha,
        ]);

        $usuario = $stmt->fetch();

        return $usuario === false ? null : $usuario;
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE usuarios SET ultimo_login_em = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}

