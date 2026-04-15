<?php

$title = 'PantaCad | Formularios';
$bodyClass = 'dashboard-page';
$oldTabName = (string) ($old['tab_name'] ?? '');
$oldLabels = is_array($old['field_label'] ?? null) && ($old['field_label'] ?? []) !== [] ? $old['field_label'] : [''];
$oldNames = is_array($old['field_name'] ?? null) && ($old['field_name'] ?? []) !== [] ? $old['field_name'] : [''];
$oldTypes = is_array($old['field_type'] ?? null) && ($old['field_type'] ?? []) !== [] ? $old['field_type'] : ['text'];
$oldPlaceholders = is_array($old['field_placeholder'] ?? null) && ($old['field_placeholder'] ?? []) !== [] ? $old['field_placeholder'] : [''];
$oldOptions = is_array($old['field_options'] ?? null) && ($old['field_options'] ?? []) !== [] ? $old['field_options'] : [''];
$fieldCount = max(count($oldLabels), count($oldNames), count($oldTypes), count($oldPlaceholders), count($oldOptions));
require dirname(__DIR__) . '/layouts/header.php';
?>
<main class="dashboard-shell">
    <header class="dashboard-topbar">
        <div class="dashboard-topbar__title">
            <img src="IMG/Logo_Nova.png" alt="Logo do sistema PantaCad">
            <div>
                <strong>PantaCad</strong>
                <span>Construtor de formulario</span>
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
                        <a class="dashboard-menu__subitem" href="index.php?action=accesses">Acessos</a>
                        <a class="dashboard-menu__subitem dashboard-menu__subitem--active" href="index.php?action=form_builder">Formulário</a>
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
            <section class="form-builder-page">
                <div class="form-builder-page__intro">
                    <h1>Construtor de abas e campos</h1>
                    <p>Crie novas abas e defina os campos que devem aparecer no Cadastro de usuarios.</p>
                </div>

                <?php if (($errorMessage ?? '') !== ''): ?>
                    <div class="error"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <?php if (($successMessage ?? '') !== ''): ?>
                    <div class="success"><?= htmlspecialchars((string) $successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <article class="form-builder-card">
                    <form method="post" action="index.php?action=store_form_layout" class="form-builder-form" id="form-builder-form">
                        <label for="form-builder-tab-name">
                            Nome da nova aba
                            <input id="form-builder-tab-name" name="tab_name" type="text" value="<?= htmlspecialchars($oldTabName, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex.: Documentos" required>
                        </label>

                        <section class="form-builder-fields">
                            <div class="form-builder-fields__header">
                                <h2>Campos da aba</h2>
                                <button type="button" id="form-builder-add-field" class="form-builder-add">Adicionar campo</button>
                            </div>

                            <div id="form-builder-field-list">
                                <?php for ($index = 0; $index < $fieldCount; $index++): ?>
                                    <article class="form-builder-field" data-field-item>
                                        <div class="form-builder-field__grid">
                                            <label>
                                                Rotulo
                                                <input type="text" name="field_label[]" value="<?= htmlspecialchars((string) ($oldLabels[$index] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex.: Numero do documento" required>
                                            </label>
                                            <label>
                                                Identificador
                                                <input type="text" name="field_name[]" value="<?= htmlspecialchars((string) ($oldNames[$index] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex.: numero_documento" required>
                                            </label>
                                            <label>
                                                Tipo
                                                <select name="field_type[]" data-field-type>
                                                    <?php $selectedType = (string) ($oldTypes[$index] ?? 'text'); ?>
                                                    <option value="text" <?= $selectedType === 'text' ? 'selected' : ''; ?>>Texto</option>
                                                    <option value="email" <?= $selectedType === 'email' ? 'selected' : ''; ?>>Email</option>
                                                    <option value="number" <?= $selectedType === 'number' ? 'selected' : ''; ?>>Numero</option>
                                                    <option value="date" <?= $selectedType === 'date' ? 'selected' : ''; ?>>Data</option>
                                                    <option value="textarea" <?= $selectedType === 'textarea' ? 'selected' : ''; ?>>Texto longo</option>
                                                    <option value="select" <?= $selectedType === 'select' ? 'selected' : ''; ?>>Selecao</option>
                                                </select>
                                            </label>
                                            <label>
                                                Placeholder
                                                <input type="text" name="field_placeholder[]" value="<?= htmlspecialchars((string) ($oldPlaceholders[$index] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex.: Digite aqui">
                                            </label>
                                            <label class="form-builder-field__options <?= $selectedType === 'select' ? '' : 'is-hidden'; ?>" data-field-options>
                                                Opcoes (separadas por virgula)
                                                <input type="text" name="field_options[]" value="<?= htmlspecialchars((string) ($oldOptions[$index] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex.: Opcao A, Opcao B">
                                            </label>
                                        </div>
                                        <button type="button" class="form-builder-field__remove" data-remove-field>Remover</button>
                                    </article>
                                <?php endfor; ?>
                            </div>
                        </section>

                        <div class="form-builder-form__actions">
                            <a href="index.php?action=users" class="users-form__cancel">Ir para Cadastro de usuarios</a>
                            <button type="submit" class="users-form__submit">Salvar aba e campos</button>
                        </div>
                    </form>
                </article>

                <?php if (($customTabs ?? []) !== []): ?>
                    <article class="form-builder-list-card">
                        <h2>Abas personalizadas criadas</h2>
                        <ul>
                            <?php foreach ($customTabs as $tab): ?>
                                <li>
                                    <strong><?= htmlspecialchars((string) ($tab['name'] ?? 'Aba'), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <span><?= count(is_array($tab['fields'] ?? null) ? $tab['fields'] : []); ?> campo(s)</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                <?php endif; ?>
            </section>
        </section>
    </section>
</main>

<template id="form-builder-field-template">
    <article class="form-builder-field" data-field-item>
        <div class="form-builder-field__grid">
            <label>
                Rotulo
                <input type="text" name="field_label[]" placeholder="Ex.: Numero do documento" required>
            </label>
            <label>
                Identificador
                <input type="text" name="field_name[]" placeholder="Ex.: numero_documento" required>
            </label>
            <label>
                Tipo
                <select name="field_type[]" data-field-type>
                    <option value="text">Texto</option>
                    <option value="email">Email</option>
                    <option value="number">Numero</option>
                    <option value="date">Data</option>
                    <option value="textarea">Texto longo</option>
                    <option value="select">Selecao</option>
                </select>
            </label>
            <label>
                Placeholder
                <input type="text" name="field_placeholder[]" placeholder="Ex.: Digite aqui">
            </label>
            <label class="form-builder-field__options is-hidden" data-field-options>
                Opcoes (separadas por virgula)
                <input type="text" name="field_options[]" placeholder="Ex.: Opcao A, Opcao B">
            </label>
        </div>
        <button type="button" class="form-builder-field__remove" data-remove-field>Remover</button>
    </article>
</template>

<script>
    (function () {
        const storageKey = 'pantacad-dashboard-menu-collapsed';
        const body = document.body;
        const toggle = document.getElementById('dashboard-toggle');
        const menuToggle = document.querySelector('[data-menu-toggle="cadastros"]');
        const menuPanel = document.querySelector('[data-menu-panel="cadastros"]');
        const menuGroup = menuToggle ? menuToggle.closest('.dashboard-menu__group') : null;
        const fieldList = document.getElementById('form-builder-field-list');
        const addFieldButton = document.getElementById('form-builder-add-field');
        const fieldTemplate = document.getElementById('form-builder-field-template');

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

        function syncFieldVisibility(fieldItem) {
            const typeSelect = fieldItem.querySelector('[data-field-type]');
            const optionsWrapper = fieldItem.querySelector('[data-field-options]');

            if (!typeSelect || !optionsWrapper) {
                return;
            }

            optionsWrapper.classList.toggle('is-hidden', typeSelect.value !== 'select');
        }

        function bindFieldEvents(fieldItem) {
            const removeButton = fieldItem.querySelector('[data-remove-field]');
            const typeSelect = fieldItem.querySelector('[data-field-type]');

            if (removeButton) {
                removeButton.addEventListener('click', function () {
                    const totalItems = fieldList.querySelectorAll('[data-field-item]').length;

                    if (totalItems <= 1) {
                        return;
                    }

                    fieldItem.remove();
                });
            }

            if (typeSelect) {
                typeSelect.addEventListener('change', function () {
                    syncFieldVisibility(fieldItem);
                });
            }

            syncFieldVisibility(fieldItem);
        }

        Array.from(document.querySelectorAll('[data-field-item]')).forEach(bindFieldEvents);

        if (addFieldButton && fieldTemplate && fieldList) {
            addFieldButton.addEventListener('click', function () {
                const fragment = fieldTemplate.content.cloneNode(true);
                const newItem = fragment.querySelector('[data-field-item]');

                if (newItem) {
                    bindFieldEvents(newItem);
                }

                fieldList.appendChild(fragment);
            });
        }
    }());
</script>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>