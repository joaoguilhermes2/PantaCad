<?php

$title = 'PantaCad | Acessos';
$bodyClass = 'dashboard-page dashboard-page--accesses';
$isEditing = is_array($editAccess ?? null);
$isCreating = (bool) ($createModalOpen ?? false) && !$isEditing;
$hasSuccessModal = ($successMessage ?? '') !== '';
$currentPage = max(1, (int) ($currentPage ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$pageQuery = '&page=' . $currentPage;
$previousPage = max(1, $currentPage - 1);
$nextPage = min($totalPages, $currentPage + 1);
$nomeValue = (string) (($old['nome'] ?? '') !== '' ? $old['nome'] : '');
$emailValue = (string) (($old['email'] ?? '') !== '' ? $old['email'] : '');
$nivelAcessoIdValue = (int) (($old['nivel_acesso_id'] ?? 0) > 0 ? $old['nivel_acesso_id'] : 0);
$ativoValue = !array_key_exists('ativo', $old ?? []) || (bool) $old['ativo'];
$editNomeValue = (string) (($old['nome'] ?? '') !== '' ? $old['nome'] : (($editAccess['nome'] ?? '') !== '' ? $editAccess['nome'] : ''));
$editEmailValue = (string) (($old['email'] ?? '') !== '' ? $old['email'] : (($editAccess['email'] ?? '') !== '' ? $editAccess['email'] : ''));
$editNivelAcessoIdValue = (int) (($old['nivel_acesso_id'] ?? 0) > 0
    ? $old['nivel_acesso_id']
    : (($editAccess['nivel_acesso_id'] ?? 0) > 0 ? $editAccess['nivel_acesso_id'] : 0));
$editAtivoValue = array_key_exists('ativo', $old ?? [])
    ? (bool) $old['ativo']
    : ($isEditing ? !empty($editAccess['ativo']) : true);
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="dashboard-shell">
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
                        <a class="dashboard-menu__subitem" href="index.php?action=users">Usuarios</a>
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
        <section class="dashboard-main">
            <section class="access-page">
                <div class="access-page__intro">
                    <h1>Acessos do sistema</h1>
                    <p>Cadastre os logins dos usuários que terão acesso ao PantaCad.</p>
                </div>

                <?php if (($errorMessage ?? '') !== ''): ?>
                    <div class="error"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <section class="access-grid">
                    <article class="access-card access-card--wide" id="access-card">
                        <div class="access-card__header">
                            <div>
                                <h2>Acessos cadastrados</h2>
                                <p>Relação atual dos usuários com login configurado.</p>
                            </div>
                            <div class="access-card__toolbar">
                                <label class="access-search" for="access-search-input" aria-label="Buscar acessos cadastrados">
                                    <input id="access-search-input" type="search" placeholder="Buscar por nome, email, nível ou status">
                                    <span class="access-search__button" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" focusable="false">
                                            <path d="M10.5 3a7.5 7.5 0 1 1-5.3 12.8A7.5 7.5 0 0 1 10.5 3zm0 2a5.5 5.5 0 1 0 3.89 1.61A5.47 5.47 0 0 0 10.5 5zm6.65 10.74 3.56 3.55-1.42 1.42-3.55-3.56 1.41-1.41z"/>
                                        </svg>
                                    </span>
                                </label>
                                <a class="access-card__create" href="index.php?action=accesses&new=1<?= $pageQuery; ?>">Novo acesso</a>
                            </div>
                        </div>

                        <div class="access-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Nível de acesso</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($accessList as $item): ?>
                                        <tr class="access-row" data-search="<?= htmlspecialchars(mb_strtolower(trim((string) $item['nome'] . ' ' . (string) $item['email'] . ' ' . (string) ($item['nivel_acesso'] ?? '') . ' ' . (!empty($item['ativo']) ? 'ativo' : 'inativo')), 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <td><?= htmlspecialchars((string) $item['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars((string) $item['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <span class="access-level"><?= htmlspecialchars((string) ($item['nivel_acesso'] ?? 'Sem nivel'), ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td>
                                                <span class="access-status <?= !empty($item['ativo']) ? 'access-status--active' : 'access-status--inactive'; ?>">
                                                    <?= !empty($item['ativo']) ? 'Ativo' : 'Inativo'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="access-actions">
                                                    <a class="access-action access-action--edit" href="index.php?action=accesses&edit=<?= (int) $item['id']; ?><?= $pageQuery; ?>" title="Editar acesso" aria-label="Editar acesso de <?= htmlspecialchars((string) $item['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                                            <path d="M6 3h8l4 4v4h-2V8h-3V5H6v14h6v2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm11.71 9.04a1 1 0 0 1 1.41 0l.84.84a1 1 0 0 1 0 1.41l-4.92 4.92L12 20l.79-3.04 4.92-4.92z"/>
                                                        </svg>
                                                    </a>
                                                    <form method="post" action="index.php?action=delete_access" class="access-deactivate-form" data-access-name="<?= htmlspecialchars((string) $item['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <input type="hidden" name="id" value="<?= (int) $item['id']; ?>">
                                                        <input type="hidden" name="page" value="<?= $currentPage; ?>">
                                                        <button
                                                            type="submit"
                                                            class="access-action access-action--delete"
                                                            title="Inativar acesso"
                                                            aria-label="Inativar acesso de <?= htmlspecialchars((string) $item['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                                                            <?= (int) $item['id'] === (int) $usuario['id'] || empty($item['ativo']) ? 'disabled' : ''; ?>
                                                        >
                                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                                <path d="M18.3 7.11 16.89 5.7 12 10.59 7.11 5.7 5.7 7.11 10.59 12 5.7 16.89l1.41 1.41L12 13.41l4.89 4.89 1.41-1.41L13.41 12z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (($accessList ?? []) === []): ?>
                                        <tr>
                                            <td colspan="5" class="access-table__empty">Nenhum acesso encontrado nesta pagina.</td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr id="access-search-empty" hidden>
                                        <td colspan="5" class="access-table__empty">Nenhum acesso corresponde Ã busca informada.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($totalPages > 1): ?>
                            <div class="access-pagination" aria-label="Paginacao de acessos">
                                <a
                                    class="access-pagination__arrow <?= $currentPage <= 1 ? 'is-disabled' : ''; ?>"
                                    href="<?= $currentPage <= 1 ? '#' : 'index.php?action=accesses&page=' . $previousPage; ?>"
                                    aria-label="Pagina anterior"
                                    <?= $currentPage <= 1 ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                                >&lt;</a>
                                <span class="access-pagination__info">Pagina <?= $currentPage; ?> de <?= $totalPages; ?></span>
                                <a
                                    class="access-pagination__arrow <?= $currentPage >= $totalPages ? 'is-disabled' : ''; ?>"
                                    href="<?= $currentPage >= $totalPages ? '#' : 'index.php?action=accesses&page=' . $nextPage; ?>"
                                    aria-label="Proxima pagina"
                                    <?= $currentPage >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                                >&gt;</a>
                            </div>
                        <?php endif; ?>
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
            <a href="index.php?action=accesses&page=<?= $currentPage; ?>" class="access-modal__close" aria-label="Fechar modal de novo acesso">x</a>

            <div class="access-modal__header">
                <h2 id="access-create-title">Novo acesso</h2>
                <p>Preencha os dados basicos para liberar o login do usuario.</p>
            </div>

            <form method="post" action="index.php?action=store_access" class="access-form access-form--modal">
                <input type="hidden" name="page" value="<?= $currentPage; ?>">

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
                    <select id="access-level-modal" name="nivel_acesso_id" class="access-form__select" required>
                        <option value="">Selecione</option>
                        <?php foreach (($accessLevels ?? []) as $nivel): ?>
                            <option value="<?= (int) $nivel['id']; ?>" <?= $nivelAcessoIdValue === (int) $nivel['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars((string) $nivel['nome'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="access-form__check">
                    <input type="checkbox" name="ativo" value="1" <?= $ativoValue ? 'checked' : ''; ?>>
                    <span>Ativar acesso imediatamente</span>
                </label>

                <div class="access-form__notice">
                    A senha inicial será definida como <strong>"123456"</strong>, sendo necessário realizar a troca na tela de "Primeiro Acesso" para acessar o sistema.
                </div>

                <div class="access-form__actions">
                    <a class="access-form__cancel" href="index.php?action=accesses&page=<?= $currentPage; ?>">Cancelar</a>
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
            <a href="index.php?action=accesses&page=<?= $currentPage; ?>" class="access-modal__close" aria-label="Fechar modal de edicao">x</a>

            <div class="access-modal__header">
                <h2 id="access-edit-title">Editar acesso</h2>
                <p>Atualize os dados do login selecionado sem sair da pagina de acessos.</p>
            </div>

            <form method="post" action="index.php?action=update_access" class="access-form access-form--modal">
                <input type="hidden" name="id" value="<?= (int) $editAccess['id']; ?>">
                <input type="hidden" name="page" value="<?= $currentPage; ?>">

                <label for="access-edit-nome">
                    Nome completo
                    <input id="access-edit-nome" name="nome" type="text" value="<?= htmlspecialchars($editNomeValue, ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label for="access-edit-email">
                    Email de acesso
                    <input id="access-edit-email" name="email" type="email" value="<?= htmlspecialchars($editEmailValue, ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label for="access-edit-level">
                    Selecione o nivel de acesso
                    <select id="access-edit-level" name="nivel_acesso_id" class="access-form__select" required>
                        <option value="">Selecione</option>
                        <?php foreach (($accessLevels ?? []) as $nivel): ?>
                            <option value="<?= (int) $nivel['id']; ?>" <?= $editNivelAcessoIdValue === (int) $nivel['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars((string) $nivel['nome'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="access-form__check">
                    <input type="checkbox" name="ativo" value="1" <?= $editAtivoValue ? 'checked' : ''; ?>>
                    <span>Manter acesso ativo</span>
                </label>

                <div class="access-form__notice">
                    A alteracao atualiza apenas os dados do login. A senha do usuario continua sob a politica atual do sistema.
                </div>

                <div class="access-form__actions">
                    <a class="access-form__cancel" href="index.php?action=accesses&page=<?= $currentPage; ?>">Cancelar</a>
                    <button type="submit" class="access-form__submit">Salvar alteracoes</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php if ($hasSuccessModal): ?>
    <div class="access-modal access-modal--success is-open" id="access-success-modal" role="dialog" aria-modal="true" aria-labelledby="access-success-title">
        <div class="access-modal__backdrop"></div>
        <div class="access-modal__dialog">
            <div class="access-modal__header access-modal__header--success access-modal__header--success-modern">
                <span class="access-modal__icon access-modal__icon--success" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M9.55 16.6 5.4 12.45l1.4-1.4 2.75 2.75 7-7 1.4 1.4-8.4 8.4z"/>
                    </svg>
                </span>
                <h2 id="access-success-title">Operação concluída</h2>
                <p><?= htmlspecialchars((string) $successMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <div class="access-modal__actions">
                <a class="access-modal__button access-modal__button--success" href="index.php?action=accesses&page=<?= $currentPage; ?>">Fechar</a>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="access-modal" id="access-deactivate-modal" role="dialog" aria-modal="true" aria-labelledby="access-deactivate-title" hidden>
    <div class="access-modal__backdrop"></div>
    <div class="access-modal__dialog">
        <div class="access-modal__header access-modal__header--success">
            <h2 id="access-deactivate-title">Confirmar inativação</h2>
            <p id="access-deactivate-message">Deseja realmente inativar este acesso?</p>
        </div>

        <div class="access-modal__actions access-modal__actions--split">
            <button type="button" class="access-modal__button access-modal__button--secondary" id="access-deactivate-cancel">Cancelar</button>
            <button type="button" class="access-modal__button access-modal__button--danger" id="access-deactivate-confirm">Inativar acesso</button>
        </div>
    </div>
</div>
<script>
    (function () {
        const storageKey = 'pantacad-dashboard-menu-collapsed';
        const body = document.body;
        const toggle = document.getElementById('dashboard-toggle');
        const menuToggle = document.querySelector('[data-menu-toggle="cadastros"]');
        const menuPanel = document.querySelector('[data-menu-panel="cadastros"]');
        const menuGroup = menuToggle ? menuToggle.closest('.dashboard-menu__group') : null;
        const accessCard = document.getElementById('access-card');
        const accessSearchInput = document.getElementById('access-search-input');
        const accessRows = document.querySelectorAll('.access-row');
        const accessSearchEmpty = document.getElementById('access-search-empty');
        const paginationArrows = document.querySelectorAll('.access-pagination__arrow:not(.is-disabled)');
        const modal = document.querySelector('.access-modal.is-open');
        const modalBackdrop = modal ? modal.querySelector('.access-modal__backdrop') : null;
        const modalClose = modal ? modal.querySelector('.access-modal__close') : null;
        const deactivateModal = document.getElementById('access-deactivate-modal');
        const deactivateBackdrop = deactivateModal ? deactivateModal.querySelector('.access-modal__backdrop') : null;
        const deactivateCancel = document.getElementById('access-deactivate-cancel');
        const deactivateConfirm = document.getElementById('access-deactivate-confirm');
        const deactivateMessage = document.getElementById('access-deactivate-message');
        const deactivateForms = document.querySelectorAll('.access-deactivate-form');
        let pendingDeactivateForm = null;

        if (toggle) {
            toggle.setAttribute('aria-expanded', String(!body.classList.contains('dashboard-menu-collapsed')));
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                const isCollapsed = body.classList.toggle('dashboard-menu-collapsed');
                toggle.setAttribute('aria-expanded', String(!isCollapsed));

                try {
                    window.localStorage.setItem(storageKey, isCollapsed ? 'true' : 'false');
                } catch (error) {
                }
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

        if (accessCard && paginationArrows.length) {
            paginationArrows.forEach(function (arrow) {
                arrow.addEventListener('click', function (event) {
                    const targetHref = arrow.getAttribute('href');

                    if (!targetHref || targetHref === '#') {
                        return;
                    }

                    event.preventDefault();

                    accessCard.classList.remove('access-card--page-next', 'access-card--page-prev');
                    void accessCard.offsetWidth;

                    accessCard.classList.add(
                        arrow.getAttribute('aria-label') === 'Proxima pagina'
                            ? 'access-card--page-next'
                            : 'access-card--page-prev'
                    );

                    window.setTimeout(function () {
                        window.location.href = targetHref;
                    }, 220);
                });
            });
        }

        if (accessSearchInput && accessRows.length) {
            accessSearchInput.addEventListener('input', function () {
                const query = accessSearchInput.value.trim().toLowerCase();
                let visibleRows = 0;

                accessRows.forEach(function (row) {
                    const haystack = row.getAttribute('data-search') || '';
                    const matches = query === '' || haystack.indexOf(query) !== -1;

                    row.hidden = !matches;

                    if (matches) {
                        visibleRows += 1;
                    }
                });

                if (accessSearchEmpty) {
                    accessSearchEmpty.hidden = query === '' || visibleRows > 0;
                }
            });
        }

        function openDeactivateModal(form) {
            if (!deactivateModal) {
                return;
            }

            pendingDeactivateForm = form;

            if (deactivateMessage) {
                const accessName = form.getAttribute('data-access-name') || 'este acesso';
                deactivateMessage.textContent = 'Deseja realmente inativar o acesso de ' + accessName + '?';
            }

            deactivateModal.hidden = false;
            deactivateModal.classList.add('is-open');
            body.classList.add('modal-open');
        }

        function closeDeactivateModal() {
            if (!deactivateModal) {
                return;
            }

            deactivateModal.classList.remove('is-open');
            deactivateModal.hidden = true;
            pendingDeactivateForm = null;

            if (!document.querySelector('.access-modal.is-open')) {
                body.classList.remove('modal-open');
            }
        }

        if (deactivateForms.length && deactivateModal) {
            deactivateForms.forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    openDeactivateModal(form);
                });
            });
        }

        if (deactivateBackdrop) {
            deactivateBackdrop.addEventListener('click', closeDeactivateModal);
        }


        if (deactivateCancel) {
            deactivateCancel.addEventListener('click', closeDeactivateModal);
        }

        if (deactivateConfirm) {
            deactivateConfirm.addEventListener('click', function () {
                if (pendingDeactivateForm) {
                    pendingDeactivateForm.submit();
                }
            });
        }

        if (modal) {
            body.classList.add('modal-open');

            if (modalBackdrop) {
                modalBackdrop.addEventListener('click', function () {
                    window.location.href = 'index.php?action=accesses&page=<?= $currentPage; ?>';
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    if (deactivateModal && !deactivateModal.hidden) {
                        closeDeactivateModal();
                        return;
                    }

                    window.location.href = 'index.php?action=accesses&page=<?= $currentPage; ?>';
                }
            });

            if (modalClose) {
                modalClose.addEventListener('click', function (event) {
                    event.preventDefault();
                    window.location.href = 'index.php?action=accesses&page=<?= $currentPage; ?>';
                });
            }
        } else {
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && deactivateModal && !deactivateModal.hidden) {
                    closeDeactivateModal();
                }
            });
        }
    }());
</script>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
