<?php

$title = 'PantaCad | Acessos';
$bodyClass = 'dashboard-page';
$isEditing = is_array($editAccess ?? null);
$isCreating = (bool) ($createModalOpen ?? false) && !$isEditing;
$nomeValue = (string) (($old['nome'] ?? '') !== '' ? $old['nome'] : '');
$emailValue = (string) (($old['email'] ?? '') !== '' ? $old['email'] : '');
$nivelAcessoValue = (string) (($old['nivel_acesso'] ?? '') !== '' ? $old['nivel_acesso'] : 'Colaborador');
$ativoValue = !array_key_exists('ativo', $old ?? []) || (bool) $old['ativo'];
$editNomeValue = (string) (($old['nome'] ?? '') !== '' ? $old['nome'] : (($editAccess['nome'] ?? '') !== '' ? $editAccess['nome'] : ''));
$editEmailValue = (string) (($old['email'] ?? '') !== '' ? $old['email'] : (($editAccess['email'] ?? '') !== '' ? $editAccess['email'] : ''));
$editAtivoValue = array_key_exists('ativo', $old ?? [])
    ? (bool) $old['ativo']
    : ($isEditing ? !empty($editAccess['ativo']) : true);
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="dashboard-shell">
    <aside class="dashboard-sidebar" id="dashboard-sidebar">
        <div>
            <div class="dashboard-sidebar__top">
                <button
                    type="button"
                    class="dashboard-toggle dashboard-toggle--sidebar"
                    id="dashboard-toggle"
                    aria-label="Ocultar ou exibir menu"
                    aria-controls="dashboard-sidebar"
                    aria-expanded="true"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <nav class="dashboard-menu" aria-label="Menu principal">
                <a class="dashboard-menu__item" href="index.php?action=dashboard">Inicio</a>
                <div class="dashboard-menu__group dashboard-menu__group--open">
                    <button
                        type="button"
                        class="dashboard-menu__item dashboard-menu__item--toggle"
                        data-menu-toggle="cadastros"
                        aria-controls="menu-cadastros"
                        aria-expanded="true"
                    >
                        Cadastros
                    </button>
                    <div class="dashboard-menu__submenu" id="menu-cadastros" data-menu-panel="cadastros">
                        <a class="dashboard-menu__subitem" href="#">Usuarios</a>
                        <a class="dashboard-menu__subitem dashboard-menu__subitem--active" href="index.php?action=accesses">Acessos</a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="dashboard-sidebar__footer">
            <a class="dashboard-sidebar__logout" href="index.php?action=logout">Sair</a>
        </div>
    </aside>

    <section class="dashboard-content">
        <header class="dashboard-topbar">
            <div class="dashboard-topbar__title">
                <img src="IMG/Logo_Nova.png" alt="Logo do sistema PantaCad">
                <div>
                    <strong>PantaCad</strong>
                    <span>Cadastro de acessos</span>
                </div>
            </div>

            <div class="dashboard-profile">
                <a class="dashboard-profile__button dashboard-profile__button--static" href="index.php?action=profile">
                    <?php if (!empty($usuario['foto_perfil'])): ?>
                        <img class="dashboard-profile__image" src="<?= htmlspecialchars((string) $usuario['foto_perfil'], ENT_QUOTES, 'UTF-8'); ?>" alt="Foto de perfil de <?= htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php else: ?>
                        <span class="dashboard-profile__avatar"><?= htmlspecialchars(strtoupper(substr((string) $usuario['nome'], 0, 1)), ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <span class="dashboard-profile__text">
                        <strong><?= htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span><?= htmlspecialchars((string) $usuario['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </span>
                </a>
            </div>
        </header>

        <section class="dashboard-main">
            <section class="access-page">
                <div class="access-page__intro">
                    <h1>Acessos do sistema</h1>
                    <p>Cadastre os logins dos usuários que terão acesso ao PantaCad. A senha inicial será definida como <strong>123456</strong> e trocada no primeiro acesso.</p>
                </div>

                <?php if (($errorMessage ?? '') !== ''): ?>
                    <div class="error"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <?php if (($successMessage ?? '') !== ''): ?>
                    <div class="success"><?= htmlspecialchars((string) $successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <section class="access-grid">
                    <article class="access-card access-card--wide">
                        <div class="access-card__header">
                            <div>
                                <h2>Acessos cadastrados</h2>
                                <p>Relacao atual dos usuarios com login configurado.</p>
                            </div>
                            <a class="access-card__create" href="index.php?action=accesses&new=1">Novo acesso</a>
                        </div>

                        <div class="access-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Nivel de Acesso</th>
                                        <th>Status</th>
                                        <th>Acoes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($accessList as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $item['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars((string) $item['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <span class="access-level"><?= htmlspecialchars((string) ($item['nivel_acesso'] ?? 'Colaborador'), ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td>
                                                <span class="access-status <?= !empty($item['ativo']) ? 'access-status--active' : 'access-status--inactive'; ?>">
                                                    <?= !empty($item['ativo']) ? 'Ativo' : 'Inativo'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="access-actions">
                                                    <a class="access-action access-action--edit" href="index.php?action=accesses&edit=<?= (int) $item['id']; ?>" title="Editar acesso" aria-label="Editar acesso de <?= htmlspecialchars((string) $item['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                                            <path d="M4 15.75V20h4.25L19.81 8.44l-4.25-4.25L4 15.75zm13.71-8.04a1.003 1.003 0 0 0 0-1.42l-2-2a1.003 1.003 0 0 0-1.42 0l-1.59 1.59 4.25 4.25 1.76-1.42z"/>
                                                        </svg>
                                                    </a>
                                                    <form method="post" action="index.php?action=delete_access" onsubmit="return confirm('Deseja realmente excluir este acesso?');">
                                                        <input type="hidden" name="id" value="<?= (int) $item['id']; ?>">
                                                        <button
                                                            type="submit"
                                                            class="access-action access-action--delete"
                                                            title="Excluir acesso"
                                                            aria-label="Excluir acesso de <?= htmlspecialchars((string) $item['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                                                            <?= (int) $item['id'] === (int) $usuario['id'] ? 'disabled' : ''; ?>
                                                        >
                                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                                <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2h4v2H4V6h4l1-2z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </article>
                </section>
            </section>
        </section>
    </section>
</main>
<?php if ($isCreating): ?>
    <div class="access-modal is-open" id="access-create-modal" role="dialog" aria-modal="true" aria-labelledby="access-create-title">
        <div class="access-modal__backdrop"></div>
        <div class="access-modal__dialog">
            <a href="index.php?action=accesses" class="access-modal__close" aria-label="Fechar modal de novo acesso">x</a>

            <div class="access-modal__header">
                <h2 id="access-create-title">Novo acesso</h2>
                <p>Preencha os dados basicos para liberar o login do usuario.</p>
            </div>

            <form method="post" action="index.php?action=store_access" class="access-form access-form--modal">
                <label for="access-nome-modal">
                    Nome completo
                    <input id="access-nome-modal" name="nome" type="text" value="<?= htmlspecialchars($nomeValue, ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label for="access-email-modal">
                    Email de acesso
                    <input id="access-email-modal" name="email" type="email" value="<?= htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label for="access-level-modal">
                    Selecione o nivel de acesso
                    <select id="access-level-modal" name="nivel_acesso" class="access-form__select" required>
                        <?php foreach (($accessLevels ?? []) as $nivel): ?>
                            <option value="<?= htmlspecialchars((string) $nivel, ENT_QUOTES, 'UTF-8'); ?>" <?= $nivelAcessoValue === $nivel ? 'selected' : ''; ?>>
                                <?= htmlspecialchars((string) $nivel, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="access-form__check">
                    <input type="checkbox" name="ativo" value="1" <?= $ativoValue ? 'checked' : ''; ?>>
                    <span>Ativar acesso imediatamente</span>
                </label>

                <div class="access-form__notice">
                    O sistema grava a senha inicial com hash no banco de dados e obriga a troca no primeiro acesso.
                </div>

                <div class="access-form__actions">
                    <a class="access-form__cancel" href="index.php?action=accesses">Cancelar</a>
                    <button type="submit" class="access-form__submit">Criar acesso</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if ($isEditing): ?>
    <div class="access-modal is-open" id="access-edit-modal" role="dialog" aria-modal="true" aria-labelledby="access-edit-title">
        <div class="access-modal__backdrop"></div>
        <div class="access-modal__dialog">
            <a href="index.php?action=accesses" class="access-modal__close" aria-label="Fechar modal de edicao">x</a>

            <div class="access-modal__header">
                <h2 id="access-edit-title">Editar acesso</h2>
                <p>Atualize os dados do login selecionado sem sair da pagina de acessos.</p>
            </div>

            <form method="post" action="index.php?action=update_access" class="access-form access-form--modal">
                <input type="hidden" name="id" value="<?= (int) $editAccess['id']; ?>">

                <label for="access-edit-nome">
                    Nome completo
                    <input id="access-edit-nome" name="nome" type="text" value="<?= htmlspecialchars($editNomeValue, ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label for="access-edit-email">
                    Email de acesso
                    <input id="access-edit-email" name="email" type="email" value="<?= htmlspecialchars($editEmailValue, ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label class="access-form__check">
                    <input type="checkbox" name="ativo" value="1" <?= $editAtivoValue ? 'checked' : ''; ?>>
                    <span>Manter acesso ativo</span>
                </label>

                <div class="access-form__notice">
                    A alteracao atualiza apenas os dados do login. A senha do usuario continua sob a politica atual do sistema.
                </div>

                <div class="access-form__actions">
                    <a class="access-form__cancel" href="index.php?action=accesses">Cancelar</a>
                    <button type="submit" class="access-form__submit">Salvar alteracoes</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<script>
    (function () {
        const body = document.body;
        const toggle = document.getElementById('dashboard-toggle');
        const menuToggle = document.querySelector('[data-menu-toggle="cadastros"]');
        const menuPanel = document.querySelector('[data-menu-panel="cadastros"]');
        const menuGroup = menuToggle ? menuToggle.closest('.dashboard-menu__group') : null;
        const modal = document.querySelector('.access-modal.is-open');
        const modalBackdrop = modal ? modal.querySelector('.access-modal__backdrop') : null;
        const modalClose = modal ? modal.querySelector('.access-modal__close') : null;

        if (toggle) {
            toggle.addEventListener('click', function () {
                const isCollapsed = body.classList.toggle('dashboard-menu-collapsed');
                toggle.setAttribute('aria-expanded', String(!isCollapsed));
            });
        }

        if (menuToggle && menuPanel) {
            menuGroup && menuGroup.classList.toggle('dashboard-menu__group--open', !menuPanel.hidden);

            menuToggle.addEventListener('click', function () {
                const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
                menuToggle.setAttribute('aria-expanded', String(!isExpanded));
                menuPanel.hidden = isExpanded;
                menuGroup && menuGroup.classList.toggle('dashboard-menu__group--open', !isExpanded);
            });
        }

        if (modal) {
            body.classList.add('modal-open');

            if (modalBackdrop) {
                modalBackdrop.addEventListener('click', function () {
                    window.location.href = 'index.php?action=accesses';
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    window.location.href = 'index.php?action=accesses';
                }
            });

            if (modalClose) {
                modalClose.addEventListener('click', function (event) {
                    event.preventDefault();
                    window.location.href = 'index.php?action=accesses';
                });
            }
        }
    }());
</script>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>