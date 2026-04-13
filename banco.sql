CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TABLE IF NOT EXISTS usuarios (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    senha_hash TEXT NOT NULL,
    nivel_acesso VARCHAR(30) NOT NULL DEFAULT 'Colaborador',
    foto_perfil VARCHAR(255) NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    ultimo_login_em TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX IF NOT EXISTS ux_usuarios_email
    ON usuarios (LOWER(email));

DROP TRIGGER IF EXISTS trg_usuarios_updated_at ON usuarios;

CREATE TRIGGER trg_usuarios_updated_at
BEFORE UPDATE ON usuarios
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();