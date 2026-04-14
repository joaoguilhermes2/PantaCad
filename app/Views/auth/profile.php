<?php

$title = 'PantaCad | Perfil';
$bodyClass = 'dashboard-page';
$nomeValue = (string) (($old['nome'] ?? '') !== '' ? $old['nome'] : ($usuario['nome'] ?? ''));
$emailValue = (string) (($old['email'] ?? '') !== '' ? $old['email'] : ($usuario['email'] ?? ''));
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="dashboard-shell">
    <header class="dashboard-topbar">
        <div class="dashboard-topbar__title">
            <img src="IMG/Logo_Nova.png" alt="Logo do sistema PantaCad">
            <div>
                <strong>PantaCad</strong>
                <span>Edicao de perfil</span>
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
                <div class="dashboard-menu__group">
                    <button
                        type="button"
                        class="dashboard-menu__item dashboard-menu__item--toggle"
                        data-menu-toggle="cadastros"
                        aria-controls="menu-cadastros"
                        aria-expanded="false"
                    >
                        Cadastros
                    </button>
                    <div class="dashboard-menu__submenu" id="menu-cadastros" data-menu-panel="cadastros" hidden>
                        <a class="dashboard-menu__subitem" href="#">Usuarios</a>
                        <a class="dashboard-menu__subitem" href="index.php?action=accesses">Acessos</a>
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
            <section class="profile-page">
                <div class="profile-page__intro">
                    <div>
                        <h1>Meu perfil</h1>
                        <p>Atualize seus dados pessoais e a imagem vinculada ao seu acesso.</p>
                    </div>
                </div>

                <?php if (($errorMessage ?? '') !== ''): ?>
                    <div class="error"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <?php if (($successMessage ?? '') !== ''): ?>
                    <div class="success"><?= htmlspecialchars((string) $successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <article class="profile-card">
                    <form method="post" action="index.php?action=update_profile" class="profile-form" enctype="multipart/form-data">
                        <div class="profile-card__photo">
                            <div class="profile-edit__preview profile-edit__preview--large" id="profile-preview" aria-live="polite">
                                <?php if (!empty($usuario['foto_perfil'])): ?>
                                    <img id="profile-preview-image" src="<?= htmlspecialchars((string) $usuario['foto_perfil'], ENT_QUOTES, 'UTF-8'); ?>" alt="Pre-visualizacao da foto de perfil">
                                    <span id="profile-preview-placeholder" hidden>Sem imagem</span>
                                <?php else: ?>
                                    <img id="profile-preview-image" alt="Pre-visualizacao da foto de perfil" hidden>
                                    <span id="profile-preview-placeholder">Sem imagem</span>
                                <?php endif; ?>
                            </div>

                            <label for="foto_perfil" class="profile-edit__cta">
                                Alterar foto
                                <input
                                    id="foto_perfil"
                                    name="foto_perfil"
                                    class="profile-edit__input"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                >
                            </label>

                            <p class="profile-card__hint">A imagem aceita JPG, PNG ou WEBP com ate 5MB.</p>
                            <p class="profile-edit__filename" id="profile-upload-filename">Nenhum arquivo selecionado</p>
                        </div>

                        <div class="profile-card__fields">
                            <label for="nome">
                                Seu nome completo
                                <input
                                    id="nome"
                                    name="nome"
                                    type="text"
                                    value="<?= htmlspecialchars($nomeValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    required
                                >
                            </label>

                            <label for="email">
                                Seu email
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="<?= htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    required
                                >
                            </label>
                        </div>

                        <div class="profile-card__actions">
                            <a href="index.php?action=dashboard" class="profile-card__back">Voltar</a>
                            <button type="submit" class="profile-card__save">Salvar alterações</button>
                        </div>
                    </form>
                </article>
            </section>
        </section>
    </section>
</main>
<script>
    (function () {
        const storageKey = 'pantacad-dashboard-menu-collapsed';
        const body = document.body;
        const toggle = document.getElementById('dashboard-toggle');
        const menuToggle = document.querySelector('[data-menu-toggle="cadastros"]');
        const menuPanel = document.querySelector('[data-menu-panel="cadastros"]');
        const menuGroup = menuToggle ? menuToggle.closest('.dashboard-menu__group') : null;
        const input = document.getElementById('foto_perfil');
        const fileName = document.getElementById('profile-upload-filename');
        const previewImage = document.getElementById('profile-preview-image');
        const placeholder = document.getElementById('profile-preview-placeholder');

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

        if (!input || !fileName || !previewImage || !placeholder) {
            return;
        }

        input.addEventListener('change', function () {
            const file = input.files && input.files[0] ? input.files[0] : null;

            if (!file) {
                fileName.textContent = 'Nenhum arquivo selecionado';
                previewImage.hidden = true;
                previewImage.removeAttribute('src');
                placeholder.hidden = false;
                return;
            }

            fileName.textContent = file.name;

            const reader = new FileReader();
            reader.onload = function (event) {
                previewImage.src = String(event.target && event.target.result ? event.target.result : '');
                previewImage.hidden = false;
                placeholder.hidden = true;
            };
            reader.readAsDataURL(file);
        });
    }());
</script>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
