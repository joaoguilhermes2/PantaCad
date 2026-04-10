<?php

$title = 'PantaCad | Login';
$bodyClass = 'login-page';
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="login-shell">
    <section class="login-hero">
        <div class="login-brand">
            <img src="IMG/Logotipo_Tetse.png" alt="Logo do sistema PantaCad">
        </div>
    </section>

    <section class="login-panel">
        <div class="login-panel__header">
            <h2>Login</h2>
            <p>Use seu email e senha para acessar o sistema.</p>
        </div>

        <form method="post" action="index.php?action=login" class="login-form">
            <label for="email">
                Email
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="<?= htmlspecialchars((string) ($email ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                    autocomplete="email"
                    placeholder="seuemail@dominio.com"
                    required
                >
            </label>

            <label for="senha">
                Senha
                <input
                    id="senha"
                    name="senha"
                    type="password"
                    autocomplete="current-password"
                    placeholder="Digite sua senha"
                    required
                >
            </label>

            <?php if (($errorMessage ?? '') !== ''): ?>
                <div class="error"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <button type="submit">Entrar</button>
        </form>
    </section>
</main>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
