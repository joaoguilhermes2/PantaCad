<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/Models/User.php';
require_once dirname(__DIR__, 2) . '/core/helpers.php';

final class AuthController
{
    private const ACCESS_LEVELS = ['Colaborador', 'Supervisor', 'Administrador', 'Dev'];

    public function index(): void
    {
        if ($this->isAuthenticated()) {
            if ($this->needsFirstAccess()) {
                redirect('index.php?action=first_access');
            }

            $this->dashboard();
            return;
        }

        view('auth/login', [
            'errorMessage' => flash('error'),
            'email' => flash('old_email', ''),
        ]);
    }

    public function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');

        if ($email === '' || $senha === '') {
            flash('error', 'Informe o email e a senha para acessar o sistema.');
            flash('old_email', $email);
            redirect('index.php');
        }

        try {
            $usuarioModel = new User(database());
            $usuario = $usuarioModel->findByCredentials($email, $senha);

            if ($usuario === null) {
                flash('error', 'Email ou senha invalidos.');
                flash('old_email', $email);
                redirect('index.php');
            }

            $usuarioModel->updateLastLogin((int) $usuario['id']);

            session_regenerate_id(true);

            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'foto_perfil' => $usuario['foto_perfil'] ?? null,
            ];

            $_SESSION['primeiro_acesso'] = in_array($usuario['primeiro_acesso'] ?? false, [true, 1, '1', 't', 'true'], true);

            if ($this->needsFirstAccess()) {
                redirect('index.php?action=first_access');
            }

            redirect('index.php');
        } catch (Throwable $exception) {
            http_response_code(500);

            view('auth/login', [
                'errorMessage' => 'Nao foi possivel validar o acesso: ' . $exception->getMessage(),
                'email' => $email,
            ]);
        }
    }

    public function firstAccess(): void
    {
        $this->ensureAuthenticated();
        $this->ensureFirstAccessRequired();

        view('auth/first-access', [
            'errorMessage' => flash('first_access_error'),
            'successMessage' => flash('first_access_success'),
        ]);
    }

    public function updateFirstAccessPassword(): void
    {
        $this->ensureAuthenticated();
        $this->ensureFirstAccessRequired();

        $novaSenha = (string) ($_POST['nova_senha'] ?? '');
        $confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

        if ($novaSenha === '' || $confirmarSenha === '') {
            flash('first_access_error', 'Preencha a nova senha e a confirmacao.');
            redirect('index.php?action=first_access');
        }

        if (strlen($novaSenha) < 6) {
            flash('first_access_error', 'A nova senha deve ter pelo menos 6 caracteres.');
            redirect('index.php?action=first_access');
        }

        if ($novaSenha !== $confirmarSenha) {
            flash('first_access_error', 'A confirmacao da senha nao confere.');
            redirect('index.php?action=first_access');
        }

        if ($novaSenha === '123456') {
            flash('first_access_error', 'A nova senha nao pode ser a senha padrao 123456.');
            redirect('index.php?action=first_access');
        }

        try {
            $usuarioModel = new User(database());
            $usuarioModel->updatePassword((int) $_SESSION['usuario']['id'], $novaSenha);

            $_SESSION['primeiro_acesso'] = false;
            flash('first_access_success', 'Senha alterada com sucesso. Agora voce ja pode usar o sistema.');

            redirect('index.php?action=dashboard');
        } catch (Throwable $exception) {
            http_response_code(500);

            view('auth/first-access', [
                'errorMessage' => 'Nao foi possivel atualizar a senha: ' . $exception->getMessage(),
                'successMessage' => '',
            ]);
        }
    }

    public function dashboard(): void
    {
        $this->ensureAuthenticated();

        if ($this->needsFirstAccess()) {
            redirect('index.php?action=first_access');
        }

        view('auth/dashboard', [
            'usuario' => $_SESSION['usuario'],
            'successMessage' => flash('first_access_success'),
        ]);
    }

    public function profile(): void
    {
        $this->ensureAuthenticated();

        $usuarioModel = new User(database());
        $usuario = $usuarioModel->findById((int) $_SESSION['usuario']['id']);

        if ($usuario === null) {
            redirect('index.php?action=logout');
        }

        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'foto_perfil' => $usuario['foto_perfil'] ?? null,
        ];

        view('auth/profile', [
            'usuario' => $_SESSION['usuario'],
            'errorMessage' => flash('profile_error'),
            'successMessage' => flash('profile_success'),
            'old' => flash('profile_old', []),
        ]);
    }

    public function accesses(): void
    {
        $this->ensureAuthenticated();

        $usuarioModel = new User(database());
        $editId = (int) ($_GET['edit'] ?? 0);
        $editAccess = $editId > 0 ? $usuarioModel->findById($editId) : null;
        $createModalOpen = isset($_GET['new']);

        view('auth/accesses', [
            'usuario' => $_SESSION['usuario'],
            'accessList' => $usuarioModel->listAll(),
            'editAccess' => $editAccess,
            'createModalOpen' => $createModalOpen,
            'accessLevels' => self::ACCESS_LEVELS,
            'errorMessage' => flash('access_error'),
            'successMessage' => flash('access_success'),
            'old' => flash('access_old', []),
        ]);
    }

    public function storeAccess(): void
    {
        $this->ensureAuthenticated();

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $nivelAcesso = trim((string) ($_POST['nivel_acesso'] ?? 'Colaborador'));
        $ativo = isset($_POST['ativo']) ? (bool) $_POST['ativo'] : true;

        flash('access_old', [
            'nome' => $nome,
            'email' => $email,
            'nivel_acesso' => $nivelAcesso,
            'ativo' => $ativo,
        ]);

        if ($nome === '' || $email === '') {
            flash('access_error', 'Preencha nome e email para criar o acesso.');
            redirect('index.php?action=accesses&new=1');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('access_error', 'Informe um email valido para o novo acesso.');
            redirect('index.php?action=accesses&new=1');
        }

        if (!in_array($nivelAcesso, self::ACCESS_LEVELS, true)) {
            flash('access_error', 'Selecione um nivel de acesso valido.');
            redirect('index.php?action=accesses&new=1');
        }

        try {
            $usuarioModel = new User(database());

            if ($usuarioModel->emailExists($email)) {
                flash('access_error', 'Ja existe um acesso cadastrado com esse email.');
                redirect('index.php?action=accesses&new=1');
            }

            $usuarioModel->createAccess($nome, $email, $nivelAcesso, $ativo);

            flash('access_old', []);
            flash('access_success', 'Acesso criado com sucesso. A senha inicial do usuario sera 123456.');
            redirect('index.php?action=accesses');
        } catch (Throwable $exception) {
            http_response_code(500);

            $usuarioModel = new User(database());

            view('auth/accesses', [
                'usuario' => $_SESSION['usuario'],
                'accessList' => $usuarioModel->listAll(),
                'errorMessage' => 'Nao foi possivel criar o acesso: ' . $exception->getMessage(),
                'successMessage' => '',
                'old' => [
                    'nome' => $nome,
                    'email' => $email,
                    'nivel_acesso' => $nivelAcesso,
                    'ativo' => $ativo,
                ],
            ]);
        }
    }

    public function updateAccess(): void
    {
        $this->ensureAuthenticated();

        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $ativo = isset($_POST['ativo']) ? (bool) $_POST['ativo'] : false;

        flash('access_old', [
            'nome' => $nome,
            'email' => $email,
            'ativo' => $ativo,
        ]);

        if ($id <= 0 || $nome === '' || $email === '') {
            flash('access_error', 'Preencha corretamente os dados para atualizar o acesso.');
            redirect('index.php?action=accesses&edit=' . $id);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('access_error', 'Informe um email valido para atualizar o acesso.');
            redirect('index.php?action=accesses&edit=' . $id);
        }

        try {
            $usuarioModel = new User(database());

            if ($usuarioModel->emailExistsForAnotherUser($email, $id)) {
                flash('access_error', 'Ja existe outro acesso cadastrado com esse email.');
                redirect('index.php?action=accesses&edit=' . $id);
            }

            $usuarioModel->updateAccess($id, $nome, $email, $ativo);

            if ((int) $_SESSION['usuario']['id'] === $id) {
                $_SESSION['usuario']['nome'] = $nome;
                $_SESSION['usuario']['email'] = $email;
            }

            flash('access_old', []);
            flash('access_success', 'Acesso atualizado com sucesso.');
            redirect('index.php?action=accesses');
        } catch (Throwable $exception) {
            http_response_code(500);
            flash('access_error', 'Nao foi possivel atualizar o acesso: ' . $exception->getMessage());
            redirect('index.php?action=accesses&edit=' . $id);
        }
    }

    public function deleteAccess(): void
    {
        $this->ensureAuthenticated();

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('access_error', 'Acesso invalido para exclusao.');
            redirect('index.php?action=accesses');
        }

        if ((int) $_SESSION['usuario']['id'] === $id) {
            flash('access_error', 'Nao e permitido excluir o usuario que esta logado no momento.');
            redirect('index.php?action=accesses');
        }

        try {
            $usuarioModel = new User(database());
            $usuarioModel->deleteAccess($id);

            flash('access_success', 'Acesso excluido com sucesso.');
            redirect('index.php?action=accesses');
        } catch (Throwable $exception) {
            http_response_code(500);
            flash('access_error', 'Nao foi possivel excluir o acesso: ' . $exception->getMessage());
            redirect('index.php?action=accesses');
        }
    }

    public function updateProfile(): void
    {
        $this->ensureAuthenticated();

        $id = (int) $_SESSION['usuario']['id'];
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $fotoPerfil = $_FILES['foto_perfil'] ?? null;

        flash('profile_old', [
            'nome' => $nome,
            'email' => $email,
        ]);

        if ($nome === '' || $email === '') {
            flash('profile_error', 'Preencha nome e email para atualizar o perfil.');
            redirect('index.php?action=profile');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('profile_error', 'Informe um email valido.');
            redirect('index.php?action=profile');
        }

        try {
            $usuarioModel = new User(database());

            if ($usuarioModel->emailExistsForAnotherUser($email, $id)) {
                flash('profile_error', 'Ja existe outro usuario com esse email.');
                redirect('index.php?action=profile');
            }

            $fotoPerfilPath = $this->storeProfilePhoto($fotoPerfil);

            $usuarioModel->updateProfile($id, $nome, $email, $fotoPerfilPath);

            $_SESSION['usuario']['nome'] = $nome;
            $_SESSION['usuario']['email'] = $email;

            if ($fotoPerfilPath !== null) {
                $_SESSION['usuario']['foto_perfil'] = $fotoPerfilPath;
            }

            flash('profile_old', []);
            flash('profile_success', 'Perfil atualizado com sucesso.');
            redirect('index.php?action=profile');
        } catch (Throwable $exception) {
            http_response_code(500);

            view('auth/profile', [
                'usuario' => $_SESSION['usuario'],
                'errorMessage' => 'Nao foi possivel atualizar o perfil: ' . $exception->getMessage(),
                'successMessage' => '',
                'old' => [
                    'nome' => $nome,
                    'email' => $email,
                ],
            ]);
        }
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }

        session_destroy();

        redirect('index.php');
    }

    private function isAuthenticated(): bool
    {
        return isset($_SESSION['usuario']) && is_array($_SESSION['usuario']);
    }

    private function needsFirstAccess(): bool
    {
        return (bool) ($_SESSION['primeiro_acesso'] ?? false);
    }

    private function ensureAuthenticated(): void
    {
        if (!$this->isAuthenticated()) {
            redirect('index.php');
        }
    }

    private function ensureFirstAccessRequired(): void
    {
        if (!$this->needsFirstAccess()) {
            redirect('index.php?action=dashboard');
        }
    }

    private function storeProfilePhoto(mixed $file): ?string
    {
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Nao foi possivel enviar a foto de perfil.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');

        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new RuntimeException('Arquivo de foto invalido.');
        }

        $mimeType = mime_content_type($tmpName) ?: '';
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowedTypes[$mimeType])) {
            throw new RuntimeException('Envie uma imagem JPG, PNG ou WEBP para a foto de perfil.');
        }

        if (((int) ($file['size'] ?? 0)) > 5 * 1024 * 1024) {
            throw new RuntimeException('A foto de perfil deve ter no maximo 5 MB.');
        }

        $directory = dirname(__DIR__, 2) . '/public/IMG/profiles';

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Nao foi possivel preparar a pasta da foto de perfil.');
        }

        $filename = sprintf(
            'perfil_%d_%s.%s',
            (int) $_SESSION['usuario']['id'],
            bin2hex(random_bytes(8)),
            $allowedTypes[$mimeType]
        );

        $destination = $directory . '/' . $filename;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new RuntimeException('Nao foi possivel salvar a foto de perfil.');
        }

        return 'IMG/profiles/' . $filename;
    }
}
