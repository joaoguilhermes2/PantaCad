<?php

$title = 'PantaCad | Primeiro Acesso';
$bodyClass = 'first-access-page';
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="login-shell">
    <section class="login-hero">
        <div class="login-brand">
            <img src="IMG/Logo_Nova.png" alt="Logo do sistema PantaCad">
        </div>
    </section>

    <section class="login-panel">
        <div class="login-panel__header">
            <h2>Primeiro acesso</h2>
            <p>Para sua seguranca, altere agora a senha padrao antes de entrar no sistema.</p>
        </div>

        <form method="post" action="index.php?action=update_first_access_password" class="login-form">
            <label for="nova_senha">
                Nova senha
                <input
                    id="nova_senha"
                    name="nova_senha"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Digite sua nova senha"
                    required
                >
            </label>

            <label for="confirmar_senha">
                Confirmar nova senha
                <input
                    id="confirmar_senha"
                    name="confirmar_senha"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Repita a nova senha"
                    required
                >
            </label>

            <?php if (($errorMessage ?? '') !== ''): ?>
                <div class="error"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (($successMessage ?? '') !== ''): ?>
                <div class="app-notification app-notification--success" role="status" aria-live="polite" data-notification>
                    <span><?= htmlspecialchars((string) $successMessage, ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" class="app-notification__close" aria-label="Fechar notificação" data-dismiss-notification>×</button>
                </div>
            <?php endif; ?>

            <button type="submit">Salvar nova senha</button>
        </form>

        <p class="login-panel__footer">A senha sera armazenada com seguranca em hash no banco de dados.</p>
    </section>
</main>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
