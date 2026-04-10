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

-- Exemplo de usuario inicial.
-- A senha abaixo sera armazenada em hash usando bcrypt.
INSERT INTO usuarios (nome, email, senha_hash)
VALUES (
    'Administrador',
    'admin@pantacad.com',
    crypt('123456', gen_salt('bf', 12))
)
ON CONFLICT DO NOTHING;

-- Exemplo de validacao de login:
-- SELECT id, nome, email
-- FROM usuarios
-- WHERE LOWER(email) = LOWER(:email)
--   AND ativo = TRUE
--   AND senha_hash = crypt(:senha, senha_hash);