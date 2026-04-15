<?php

$title = 'PantaCad | Usuarios';
$bodyClass = 'dashboard-page';
$defaultTabs = [
    [
        'id' => 'dados-pessoais',
        'name' => 'Dados Pessoais',
        'fields' => [
            [
                'name' => 'nome',
                'label' => 'Nome completo',
                'type' => 'text',
                'placeholder' => 'Digite o nome completo',
                'options' => [],
            ],
            [
                'name' => 'cpf',
                'label' => 'CPF',
                'type' => 'text',
                'placeholder' => '000.000.000-00',
                'options' => [],
            ],
            [
                'name' => 'data_nascimento',
                'label' => 'Data de nascimento',
                'type' => 'date',
                'placeholder' => '',
                'options' => [],
            ],
            [
                'name' => 'genero',
                'label' => 'Genero',
                'type' => 'select',
                'placeholder' => '',
                'options' => ['Feminino', 'Masculino', 'Outro'],
            ],
        ],
    ],
    [
        'id' => 'contato',
        'name' => 'Contato',
        'fields' => [
            [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'nome@empresa.com',
                'options' => [],
            ],
            [
                'name' => 'telefone',
                'label' => 'Telefone',
                'type' => 'text',
                'placeholder' => '(00) 00000-0000',
                'options' => [],
            ],
            [
                'name' => 'celular',
                'label' => 'Celular',
                'type' => 'text',
                'placeholder' => '(00) 00000-0000',
                'options' => [],
            ],
        ],
    ],
    [
        'id' => 'endereco',
        'name' => 'Endereco',
        'fields' => [
            [
                'name' => 'cep',
                'label' => 'CEP',
                'type' => 'text',
                'placeholder' => '00000-000',
                'options' => [],
            ],
            [
                'name' => 'logradouro',
                'label' => 'Logradouro',
                'type' => 'text',
                'placeholder' => 'Rua, avenida, etc.',
                'options' => [],
            ],
            [
                'name' => 'numero',
                'label' => 'Numero',
                'type' => 'text',
                'placeholder' => 'Ex.: 120',
                'options' => [],
            ],
            [
                'name' => 'bairro',
                'label' => 'Bairro',
                'type' => 'text',
                'placeholder' => 'Bairro',
                'options' => [],
            ],
            [
                'name' => 'cidade',
                'label' => 'Cidade',
                'type' => 'text',
                'placeholder' => 'Cidade',
                'options' => [],
            ],
            [
                'name' => 'uf',
                'label' => 'UF',
                'type' => 'text',
                'placeholder' => 'MS',
                'options' => [],
            ],
        ],
    ],
];
$tabs = array_merge($defaultTabs, is_array($customTabs ?? null) ? $customTabs : []);
$firstTabId = (string) (($tabs[0]['id'] ?? 'dados-pessoais'));
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
                        <a class="dashboard-menu__subitem" href="index.php?action=form_builder">Formulário</a>
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
                        <?php foreach ($tabs as $index => $tab): ?>
                            <button
                                type="button"
                                class="users-tabs__button <?= $index === 0 ? 'is-active' : ''; ?>"
                                data-user-tab="<?= htmlspecialchars((string) $tab['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                role="tab"
                                aria-selected="<?= $index === 0 ? 'true' : 'false'; ?>"
                            >
                                <?= htmlspecialchars((string) $tab['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <form class="users-form" action="#" method="post" autocomplete="off">
                        <?php foreach ($tabs as $tabIndex => $tab): ?>
                            <section class="users-tab-panel <?= $tabIndex === 0 ? 'is-active' : ''; ?>" data-user-panel="<?= htmlspecialchars((string) $tab['id'], ENT_QUOTES, 'UTF-8'); ?>" role="tabpanel" <?= $tabIndex === 0 ? '' : 'hidden'; ?>>
                                <fieldset class="users-section">
                                    <legend><?= htmlspecialchars((string) $tab['name'], ENT_QUOTES, 'UTF-8'); ?></legend>
                                    <div class="users-form__grid <?= (string) $tab['id'] === 'endereco' ? 'users-form__grid--address' : ''; ?>">
                                        <?php foreach (($tab['fields'] ?? []) as $field): ?>
                                            <label for="usuario-<?= htmlspecialchars((string) $tab['id'] . '-' . (string) ($field['name'] ?? 'campo'), ENT_QUOTES, 'UTF-8'); ?>" class="<?= (string) ($field['name'] ?? '') === 'logradouro' ? 'users-form__field--wide' : ''; ?>">
                                                <?= htmlspecialchars((string) ($field['label'] ?? 'Campo'), ENT_QUOTES, 'UTF-8'); ?>
                                                <?php $fieldType = (string) ($field['type'] ?? 'text'); ?>
                                                <?php if ($fieldType === 'textarea'): ?>
                                                    <textarea
                                                        id="usuario-<?= htmlspecialchars((string) $tab['id'] . '-' . (string) ($field['name'] ?? 'campo'), ENT_QUOTES, 'UTF-8'); ?>"
                                                        name="<?= htmlspecialchars((string) ($field['name'] ?? 'campo'), ENT_QUOTES, 'UTF-8'); ?>"
                                                        placeholder="<?= htmlspecialchars((string) ($field['placeholder'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                    ></textarea>
                                                <?php elseif ($fieldType === 'select'): ?>
                                                    <select id="usuario-<?= htmlspecialchars((string) $tab['id'] . '-' . (string) ($field['name'] ?? 'campo'), ENT_QUOTES, 'UTF-8'); ?>" name="<?= htmlspecialchars((string) ($field['name'] ?? 'campo'), ENT_QUOTES, 'UTF-8'); ?>">
                                                        <option value="">Selecione</option>
                                                        <?php foreach (($field['options'] ?? []) as $option): ?>
                                                            <option value="<?= htmlspecialchars((string) $option, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars((string) $option, ENT_QUOTES, 'UTF-8'); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input
                                                        id="usuario-<?= htmlspecialchars((string) $tab['id'] . '-' . (string) ($field['name'] ?? 'campo'), ENT_QUOTES, 'UTF-8'); ?>"
                                                        name="<?= htmlspecialchars((string) ($field['name'] ?? 'campo'), ENT_QUOTES, 'UTF-8'); ?>"
                                                        type="<?= htmlspecialchars($fieldType, ENT_QUOTES, 'UTF-8'); ?>"
                                                        placeholder="<?= htmlspecialchars((string) ($field['placeholder'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                        <?= (string) ($field['name'] ?? '') === 'uf' ? 'maxlength="2"' : ''; ?>
                                                    >
                                                <?php endif; ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </fieldset>
                            </section>
                        <?php endforeach; ?>

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