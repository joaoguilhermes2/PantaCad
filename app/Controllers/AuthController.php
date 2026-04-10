<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/Models/User.php';
require_once dirname(__DIR__, 2) . '/core/helpers.php';

final class AuthController
{
    public function index(): void
    {
        if ($this->isAuthenticated()) {
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
            ];

            redirect('index.php');
        } catch (Throwable $exception) {
            http_response_code(500);

            view('auth/login', [
                'errorMessage' => 'Nao foi possivel validar o acesso: ' . $exception->getMessage(),
                'email' => $email,
            ]);
        }
    }

    public function dashboard(): void
    {
        $this->ensureAuthenticated();

        view('auth/dashboard', [
            'usuario' => $_SESSION['usuario'],
        ]);
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

    private function ensureAuthenticated(): void
    {
        if (!$this->isAuthenticated()) {
            redirect('index.php');
        }
    }
}

