<?php

$title = 'PantaCad | Painel';
$bodyClass = 'dashboard-page';
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="card">
    <section class="welcome">
        <div>
            <h1>Bem-vindo ao PantaCad</h1>
            <p>Seu acesso foi validado com sucesso.</p>
        </div>

        <div class="user-box">
            <strong>Nome</strong>
            <span><?= htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
            <strong>Email</strong>
            <span><?= htmlspecialchars((string) $usuario['email'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <a class="logout-link" href="index.php?action=logout">Sair do sistema</a>
    </section>
</main>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

