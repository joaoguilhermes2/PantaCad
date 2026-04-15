CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TABLE IF NOT EXISTS niveis_acesso (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS usuarios (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    senha_hash TEXT NOT NULL,
    nivel_acesso_id BIGINT NOT NULL,
    foto_perfil VARCHAR(255) NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    ultimo_login_em TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_nivel_acesso
        FOREIGN KEY (nivel_acesso_id) REFERENCES niveis_acesso (id)
);

CREATE TABLE IF NOT EXISTS formularios_layout (
    id BIGSERIAL PRIMARY KEY,
    nome_aba VARCHAR(120) NOT NULL,
    identificador_aba VARCHAR(120) NOT NULL,
    rotulo VARCHAR(150) NOT NULL,
    identificador VARCHAR(120) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    placeholder VARCHAR(255) NOT NULL DEFAULT '',
    opcoes JSONB NOT NULL DEFAULT '[]'::jsonb,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_por_usuario_id BIGINT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_formularios_layout_usuario
        FOREIGN KEY (criado_por_usuario_id) REFERENCES usuarios (id)
);

INSERT INTO niveis_acesso (nome)
VALUES
    ('Colaborador'),
    ('Supervisor'),
    ('Administrador'),
    ('Dev')
ON CONFLICT (nome) DO NOTHING;

CREATE UNIQUE INDEX IF NOT EXISTS ux_usuarios_email
    ON usuarios (LOWER(email));

CREATE UNIQUE INDEX IF NOT EXISTS ux_formularios_layout_identificador_aba
    ON formularios_layout (LOWER(identificador_aba), LOWER(identificador));

/* Criacao das triggers */

CREATE TRIGGER trg_niveis_acesso_updated_at
BEFORE UPDATE ON niveis_acesso
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_usuarios_updated_at
BEFORE UPDATE ON usuarios
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_formularios_layout_updated_at
BEFORE UPDATE ON formularios_layout
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

/* Caso necessario, deletar

DROP TRIGGER IF EXISTS trg_niveis_acesso_updated_at ON niveis_acesso;
DROP TRIGGER IF EXISTS trg_usuarios_updated_at ON usuarios;
DROP TRIGGER IF EXISTS trg_formularios_layout_updated_at ON formularios_layout; */