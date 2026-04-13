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
            "SELECT id,
                    nome,
                    email,
                    foto_perfil,
                    (senha_hash = crypt('123456', senha_hash)) AS primeiro_acesso
             FROM usuarios
             WHERE LOWER(email) = LOWER(:email)
               AND ativo = TRUE
               AND senha_hash = crypt(:senha, senha_hash)
             LIMIT 1"
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

    public function updatePassword(int $id, string $novaSenha): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios
             SET senha_hash = crypt(:senha, gen_salt(\'bf\', 12))
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'senha' => $novaSenha,
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nome, email, foto_perfil, ativo, nivel_acesso
             FROM usuarios
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch();

        return $usuario === false ? null : $usuario;
    }

    public function updateProfile(int $id, string $nome, string $email, ?string $fotoPerfil = null): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios
             SET nome = :nome,
                 email = :email,
                 foto_perfil = COALESCE(:foto_perfil, foto_perfil)
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'email' => $email,
            'foto_perfil' => $fotoPerfil,
        ]);
    }

    public function emailExistsForAnotherUser(string $email, int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1
             FROM usuarios
             WHERE LOWER(email) = LOWER(:email)
               AND id <> :id
             LIMIT 1'
        );

        $stmt->execute([
            'email' => $email,
            'id' => $id,
        ]);

        return $stmt->fetchColumn() !== false;
    }

    public function listAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id,
                    nome,
                    email,
                    nivel_acesso,
                    ativo,
                    foto_perfil,
                    created_at
             FROM usuarios
             ORDER BY nome ASC'
        );

        return $stmt->fetchAll() ?: [];
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1
             FROM usuarios
             WHERE LOWER(email) = LOWER(:email)
             LIMIT 1'
        );

        $stmt->execute(['email' => $email]);

        return $stmt->fetchColumn() !== false;
    }

    public function createAccess(string $nome, string $email, string $nivelAcesso, bool $ativo = true): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (nome, email, senha_hash, nivel_acesso, ativo)
             VALUES (
                :nome,
                :email,
                crypt(\'123456\', gen_salt(\'bf\', 12)),
                :nivel_acesso,
                :ativo
             )'
        );

        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'nivel_acesso' => $nivelAcesso,
            'ativo' => $ativo,
        ]);
    }

    public function updateAccess(int $id, string $nome, string $email, bool $ativo): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios
             SET nome = :nome,
                 email = :email,
                 ativo = :ativo
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'email' => $email,
            'ativo' => $ativo,
        ]);
    }

    public function deleteAccess(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}

