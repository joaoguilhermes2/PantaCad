<?php

$title = 'PantaCad | Login';
$bodyClass = 'login-page';
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="card">
    <section>
        <h1>Acesso ao sistema</h1>
        <p>Entre com seu email e senha para iniciar a sessao.</p>

        <form method="post" action="index.php?action=login">
            <label for="email">
                Email
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="<?= htmlspecialchars((string) ($email ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                    autocomplete="email"
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
                    required
                >
            </label>

            <button type="submit">Entrar</button>
        </form>

        <?php if (($errorMessage ?? '') !== ''): ?>
            <div class="error"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
    </section>
</main>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

