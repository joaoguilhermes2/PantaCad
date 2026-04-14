<?php

$title = 'PantaCad | Usuarios';
$bodyClass = 'dashboard-page';
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="dashboard-shell">
    <header class="dashboard-topbar">
        <div class="dashboard-topbar__title">
            <img src="IMG/Logo_Nova.png" alt="Logo do sistema PantaCad">
            <div>
                <strong>PantaCad</strong>
                <span>Cadastro de usuarios</span>
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
                        <a class="dashboard-menu__subitem dashboard-menu__subitem--active" href="index.php?action=users">Usuarios</a>
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
            <section class="users-page">
                <div class="users-page__intro">
                    <h1>Cadastro de usuarios</h1>
                    <p>Preencha os dados do usuario por abas para organizar o cadastro.</p>
                </div>

                <article class="users-card">
                    <div class="users-tabs" role="tablist" aria-label="Abas do cadastro de usuario">
                        <button type="button" class="users-tabs__button is-active" data-user-tab="dados-pessoais" role="tab" aria-selected="true">Dados Pessoais</button>
                        <button type="button" class="users-tabs__button" data-user-tab="contato" role="tab" aria-selected="false">Contato</button>
                        <button type="button" class="users-tabs__button" data-user-tab="endereco" role="tab" aria-selected="false">Endereco</button>
                    </div>

                    <form class="users-form" action="#" method="post" autocomplete="off">
                        <section class="users-tab-panel is-active" data-user-panel="dados-pessoais" role="tabpanel">
                            <fieldset class="users-section">
                                <legend>Informacoes Gerais</legend>
                                <div class="users-form__grid">
                                    <label for="usuario-nome">
                                        Nome completo
                                        <input id="usuario-nome" name="nome" type="text" placeholder="Digite o nome completo">
                                    </label>
                                    <label for="usuario-cpf">
                                        CPF
                                        <input id="usuario-cpf" name="cpf" type="text" placeholder="000.000.000-00">
                                    </label>
                                    <label for="usuario-data-nascimento">
                                        Data de nascimento
                                        <input id="usuario-data-nascimento" name="data_nascimento" type="date">
                                    </label>
                                    <label for="usuario-genero">
                                        Genero
                                        <select id="usuario-genero" name="genero">
                                            <option value="">Selecione</option>
                                            <option value="feminino">Feminino</option>
                                            <option value="masculino">Masculino</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </label>
                                </div>
                            </fieldset>
                        </section>

                        <section class="users-tab-panel" data-user-panel="contato" role="tabpanel" hidden>
                            <fieldset class="users-section">
                                <legend>Canais de Contato</legend>
                                <div class="users-form__grid">
                                    <label for="usuario-email">
                                        Email
                                        <input id="usuario-email" name="email" type="email" placeholder="nome@empresa.com">
                                    </label>
                                    <label for="usuario-telefone">
                                        Telefone
                                        <input id="usuario-telefone" name="telefone" type="text" placeholder="(00) 00000-0000">
                                    </label>
                                    <label for="usuario-celular">
                                        Celular
                                        <input id="usuario-celular" name="celular" type="text" placeholder="(00) 00000-0000">
                                    </label>
                                </div>
                            </fieldset>
                        </section>

                        <section class="users-tab-panel" data-user-panel="endereco" role="tabpanel" hidden>
                            <fieldset class="users-section">
                                <legend>Endereco Residencial</legend>
                                <div class="users-form__grid users-form__grid--address">
                                    <label for="usuario-cep">
                                        CEP
                                        <input id="usuario-cep" name="cep" type="text" placeholder="00000-000">
                                    </label>
                                    <label for="usuario-logradouro" class="users-form__field--wide">
                                        Logradouro
                                        <input id="usuario-logradouro" name="logradouro" type="text" placeholder="Rua, avenida, etc.">
                                    </label>
                                    <label for="usuario-numero">
                                        Numero
                                        <input id="usuario-numero" name="numero" type="text" placeholder="Ex.: 120">
                                    </label>
                                    <label for="usuario-bairro">
                                        Bairro
                                        <input id="usuario-bairro" name="bairro" type="text" placeholder="Bairro">
                                    </label>
                                    <label for="usuario-cidade">
                                        Cidade
                                        <input id="usuario-cidade" name="cidade" type="text" placeholder="Cidade">
                                    </label>
                                    <label for="usuario-uf">
                                        UF
                                        <input id="usuario-uf" name="uf" type="text" maxlength="2" placeholder="MS">
                                    </label>
                                </div>
                            </fieldset>
                        </section>

                        <div class="users-form__actions">
                            <a href="index.php?action=dashboard" class="users-form__cancel">Voltar</a>
                            <button type="submit" class="users-form__submit">Salvar cadastro</button>
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
        const tabButtons = Array.from(document.querySelectorAll('[data-user-tab]'));
        const tabPanels = Array.from(document.querySelectorAll('[data-user-panel]'));

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

        tabButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const target = button.getAttribute('data-user-tab');

                tabButtons.forEach(function (item) {
                    const isActive = item === button;
                    item.classList.toggle('is-active', isActive);
                    item.setAttribute('aria-selected', String(isActive));
                });

                tabPanels.forEach(function (panel) {
                    const isActive = panel.getAttribute('data-user-panel') === target;
                    panel.classList.toggle('is-active', isActive);
                    panel.hidden = !isActive;
                });
            });
        });
    }());
</script>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>