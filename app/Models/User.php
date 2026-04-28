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

    public function findActiveByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nome, email
             FROM usuarios
             WHERE LOWER(email) = LOWER(:email)
               AND ativo = TRUE
             LIMIT 1'
        );

        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        return $usuario === false ? null : $usuario;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id,
                    u.nome,
                    u.email,
                    u.foto_perfil,
                    u.ativo,
                    u.nivel_acesso_id,
                    na.nome AS nivel_acesso
             FROM usuarios u
             LEFT JOIN niveis_acesso na ON na.id = u.nivel_acesso_id
             WHERE u.id = :id
             LIMIT 1'
        );

        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch();

        if ($usuario === false) {
            return null;
        }

        $usuario['subgrupo_acesso_ids'] = $this->listUserAccessSubgroupIds($id);

        return $usuario;
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

    public function countAll(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM usuarios');

        return (int) $stmt->fetchColumn();
    }

    public function listAllPaginated(int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id,
                    u.nome,
                    u.email,
                    u.nivel_acesso_id,
                    na.nome AS nivel_acesso,
                    COALESCE(STRING_AGG(sa.nome, \', \' ORDER BY sa.nome) FILTER (WHERE sa.id IS NOT NULL), \'\') AS subgrupos_acesso,
                    u.ativo,
                    u.foto_perfil,
                    u.created_at
             FROM usuarios u
             LEFT JOIN niveis_acesso na ON na.id = u.nivel_acesso_id
             LEFT JOIN usuarios_subgrupos_acesso usa ON usa.usuario_id = u.id
             LEFT JOIN subgrupos_acesso sa ON sa.id = usa.subgrupo_acesso_id
             GROUP BY u.id, na.nome
             ORDER BY u.nome ASC
             LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function listAccessLevels(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nome
             FROM niveis_acesso
             WHERE ativo = TRUE
             ORDER BY nome ASC'
        );

        return $stmt->fetchAll() ?: [];
    }

    public function listAccessSubgroups(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nome
             FROM subgrupos_acesso
             WHERE ativo = TRUE
             ORDER BY nome ASC'
        );

        return $stmt->fetchAll() ?: [];
    }

    public function listUserAccessSubgroups(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT sa.id, sa.nome
             FROM usuarios_subgrupos_acesso usa
             INNER JOIN subgrupos_acesso sa ON sa.id = usa.subgrupo_acesso_id
             WHERE usa.usuario_id = :usuario_id
               AND sa.ativo = TRUE
             ORDER BY sa.nome ASC'
        );

        $stmt->execute(['usuario_id' => $usuarioId]);

        return $stmt->fetchAll() ?: [];
    }

    public function accessLevelExists(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1
             FROM niveis_acesso
             WHERE id = :id
               AND ativo = TRUE
             LIMIT 1'
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetchColumn() !== false;
    }

    public function accessSubgroupsExist(array $ids): bool
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0)));

        if ($ids === []) {
            return false;
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM subgrupos_acesso
             WHERE ativo = TRUE
               AND id IN ($placeholders)"
        );

        $stmt->execute($ids);

        return (int) $stmt->fetchColumn() === count($ids);
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

    public function createAccess(string $nome, string $email, int $nivelAcessoId, array $subgrupoAcessoIds, bool $ativo = true): void
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO usuarios (nome, email, senha_hash, nivel_acesso_id, ativo)
                 VALUES (
                    :nome,
                    :email,
                    crypt(\'123456\', gen_salt(\'bf\', 12)),
                    :nivel_acesso_id,
                    :ativo
                 )
                 RETURNING id'
            );

            $stmt->execute([
                'nome' => $nome,
                'email' => $email,
                'nivel_acesso_id' => $nivelAcessoId,
                'ativo' => $ativo,
            ]);

            $usuarioId = (int) $stmt->fetchColumn();
            $this->syncAccessSubgroups($usuarioId, $subgrupoAcessoIds);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function updateAccess(int $id, string $nome, string $email, int $nivelAcessoId, array $subgrupoAcessoIds, bool $ativo): void
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE usuarios
                 SET nome = :nome,
                     email = :email,
                     nivel_acesso_id = :nivel_acesso_id,
                     ativo = :ativo
                 WHERE id = :id'
            );

            $stmt->execute([
                'id' => $id,
                'nome' => $nome,
                'email' => $email,
                'nivel_acesso_id' => $nivelAcessoId,
                'ativo' => $ativo,
            ]);

            $this->syncAccessSubgroups($id, $subgrupoAcessoIds);

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function deactivateAccess(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios
             SET ativo = FALSE
             WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
    }

    private function listUserAccessSubgroupIds(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT subgrupo_acesso_id
             FROM usuarios_subgrupos_acesso
             WHERE usuario_id = :usuario_id'
        );

        $stmt->execute(['usuario_id' => $usuarioId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
    }

    private function syncAccessSubgroups(int $usuarioId, array $subgrupoAcessoIds): void
    {
        $subgrupoAcessoIds = array_values(array_unique(array_filter(array_map('intval', $subgrupoAcessoIds), static fn (int $id): bool => $id > 0)));

        $deleteStmt = $this->pdo->prepare('DELETE FROM usuarios_subgrupos_acesso WHERE usuario_id = :usuario_id');
        $deleteStmt->execute(['usuario_id' => $usuarioId]);

        if ($subgrupoAcessoIds === []) {
            return;
        }

        $insertStmt = $this->pdo->prepare(
            'INSERT INTO usuarios_subgrupos_acesso (usuario_id, subgrupo_acesso_id)
             VALUES (:usuario_id, :subgrupo_acesso_id)'
        );

        foreach ($subgrupoAcessoIds as $subgrupoAcessoId) {
            $insertStmt->execute([
                'usuario_id' => $usuarioId,
                'subgrupo_acesso_id' => $subgrupoAcessoId,
            ]);
        }
    }
}

