<?php

$title = 'PantaCad | Redefinir Senha';
$bodyClass = 'login-page';
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
            <h2>Redefinir senha</h2>
            <p>Informe seu email de acesso e cadastre uma nova senha.</p>
        </div>

        <form method="post" action="index.php?action=request_password_reset" class="login-form">
            <label for="reset-email">
                Email
                <input
                    id="reset-email"
                    name="email"
                    type="email"
                    value="<?= htmlspecialchars((string) ($email ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                    autocomplete="email"
                    placeholder="seuemail@dominio.com"
                    required
                >
            </label>

            <label for="reset-nova-senha">
                Nova senha
                <input
                    id="reset-nova-senha"
                    name="nova_senha"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Digite sua nova senha"
                    required
                >
            </label>

            <label for="reset-confirmar-senha">
                Confirmar nova senha
                <input
                    id="reset-confirmar-senha"
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
                <div class="success"><?= htmlspecialchars((string) $successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <button type="submit">Redefinir senha</button>
        </form>

        <p class="login-panel__footer">
            <a href="index.php">Voltar para o login</a>
        </p>
    </section>
</main>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
