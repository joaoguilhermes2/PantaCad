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
                        <label for="form-builder-tab-name" class="form-builder-form__tab-name">
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
                                                Placeholder
                                                <input type="text" name="field_placeholder[]" value="<?= htmlspecialchars((string) ($oldPlaceholders[$index] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex.: Digite aqui">
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
                                            <label class="form-builder-field__options <?= $selectedType === 'select' ? '' : 'is-hidden'; ?>" data-field-options>
                                                Opcoes (separadas por virgula)
                                                <input type="text" name="field_options[]" value="<?= htmlspecialchars((string) ($oldOptions[$index] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex.: Opcao A, Opcao B">
                                            </label>
                                        </div>
                                        <button type="button" class="form-builder-field__remove" data-remove-field>Remover</button>
                                    </article>
                                <?php endfor; ?>
                            </div>

                            <div class="form-builder-pagination" id="form-builder-pagination" hidden>
                                <button type="button" class="form-builder-pagination__arrow" id="form-builder-prev-page" aria-label="Pagina anterior">&lt;</button>
                                <span class="form-builder-pagination__info" id="form-builder-page-info">Pagina 1 de 1</span>
                                <button type="button" class="form-builder-pagination__arrow" id="form-builder-next-page" aria-label="Proxima pagina">&gt;</button>
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
                                <?php
                                $tabPayload = json_encode([
                                    'name' => (string) ($tab['name'] ?? 'Aba'),
                                    'fields' => is_array($tab['fields'] ?? null) ? $tab['fields'] : [],
                                ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS);
                                ?>
                                <li>
                                    <strong><?= htmlspecialchars((string) ($tab['name'] ?? 'Aba'), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <div class="form-builder-tab-actions">
                                        <button
                                            type="button"
                                            class="form-builder-tab-action form-builder-tab-action--edit"
                                            aria-label="Editar aba <?= htmlspecialchars((string) ($tab['name'] ?? 'Aba'), ENT_QUOTES, 'UTF-8'); ?>"
                                            data-open-tab-edit
                                            data-tab='<?= htmlspecialchars((string) ($tabPayload ?: '{"name":"Aba","fields":[]}'), ENT_QUOTES, 'UTF-8'); ?>'
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M6 3h8l4 4v4h-2V8h-3V5H6v14h6v2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm11.71 9.04a1 1 0 0 1 1.41 0l.84.84a1 1 0 0 1 0 1.41l-4.92 4.92L12 20l.79-3.04 4.92-4.92z"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="form-builder-tab-action form-builder-tab-action--delete" aria-label="Excluir aba <?= htmlspecialchars((string) ($tab['name'] ?? 'Aba'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M18.3 7.11 16.89 5.7 12 10.59 7.11 5.7 5.7 7.11 10.59 12 5.7 16.89l1.41 1.41L12 13.41l4.89 4.89 1.41-1.41L13.41 12z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                <?php endif; ?>
            </section>
        </section>
    </section>
</main>

<div class="access-modal form-builder-edit-modal" id="form-builder-edit-modal" role="dialog" aria-modal="true" aria-labelledby="form-builder-edit-title" hidden>
    <div class="access-modal__backdrop" data-close-tab-edit></div>
    <div class="access-modal__dialog">
        <button type="button" class="access-modal__close" data-close-tab-edit aria-label="Fechar modal de edicao da aba">x</button>

        <div class="access-modal__header">
            <h2 id="form-builder-edit-title">Editar aba personalizada</h2>
            <p>Revise o nome da aba e os campos cadastrados.</p>
        </div>

        <div class="form-builder-edit-modal__body">
            <label for="form-builder-edit-tab-name">
                Nome da aba
                <input id="form-builder-edit-tab-name" type="text">
            </label>

            <div class="form-builder-edit-modal__fields">
                <h3>Campos cadastrados</h3>
                <div id="form-builder-edit-fields"></div>
            </div>
        </div>

        <div class="access-modal__actions">
            <button type="button" class="access-modal__button access-modal__button--secondary" data-close-tab-edit>Fechar</button>
        </div>
    </div>
</div>

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
                Placeholder
                <input type="text" name="field_placeholder[]" placeholder="Ex.: Digite aqui">
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
        const fieldPagination = document.getElementById('form-builder-pagination');
        const fieldPrevPageButton = document.getElementById('form-builder-prev-page');
        const fieldNextPageButton = document.getElementById('form-builder-next-page');
        const fieldPageInfo = document.getElementById('form-builder-page-info');
        const fieldPageSize = 3;
        let currentFieldPage = 1;
        const tabEditModal = document.getElementById('form-builder-edit-modal');
        const tabEditFields = document.getElementById('form-builder-edit-fields');
        const tabEditName = document.getElementById('form-builder-edit-tab-name');
        const tabEditButtons = Array.from(document.querySelectorAll('[data-open-tab-edit]'));
        const tabEditCloseButtons = Array.from(document.querySelectorAll('[data-close-tab-edit]'));

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

        function getFieldItems() {
            if (!fieldList) {
                return [];
            }

            return Array.from(fieldList.querySelectorAll('[data-field-item]'));
        }

        function updateFieldPagination() {
            const items = getFieldItems();
            const totalPages = Math.max(1, Math.ceil(items.length / fieldPageSize));

            if (currentFieldPage > totalPages) {
                currentFieldPage = totalPages;
            }

            const startIndex = (currentFieldPage - 1) * fieldPageSize;
            const endIndex = startIndex + fieldPageSize;

            items.forEach(function (item, index) {
                item.hidden = index < startIndex || index >= endIndex;
            });

            if (fieldPagination) {
                fieldPagination.hidden = items.length <= fieldPageSize;
            }

            if (fieldPageInfo) {
                fieldPageInfo.textContent = 'Pagina ' + String(currentFieldPage) + ' de ' + String(totalPages);
            }

            if (fieldPrevPageButton) {
                const isDisabled = currentFieldPage <= 1;
                fieldPrevPageButton.disabled = isDisabled;
                fieldPrevPageButton.classList.toggle('is-disabled', isDisabled);
            }

            if (fieldNextPageButton) {
                const isDisabled = currentFieldPage >= totalPages;
                fieldNextPageButton.disabled = isDisabled;
                fieldNextPageButton.classList.toggle('is-disabled', isDisabled);
            }
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
                    updateFieldPagination();
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
                currentFieldPage = Math.ceil(getFieldItems().length / fieldPageSize);
                updateFieldPagination();
            });
        }

        if (fieldPrevPageButton) {
            fieldPrevPageButton.addEventListener('click', function () {
                if (currentFieldPage <= 1) {
                    return;
                }

                currentFieldPage -= 1;
                updateFieldPagination();
            });
        }

        if (fieldNextPageButton) {
            fieldNextPageButton.addEventListener('click', function () {
                const totalPages = Math.max(1, Math.ceil(getFieldItems().length / fieldPageSize));

                if (currentFieldPage >= totalPages) {
                    return;
                }

                currentFieldPage += 1;
                updateFieldPagination();
            });
        }

        updateFieldPagination();

        function renderTabFields(fields) {
            if (!tabEditFields) {
                return;
            }

            tabEditFields.innerHTML = '';

            if (!Array.isArray(fields) || fields.length === 0) {
                const emptyState = document.createElement('p');
                emptyState.className = 'form-builder-edit-modal__empty';
                emptyState.textContent = 'Nenhum campo cadastrado nesta aba.';
                tabEditFields.appendChild(emptyState);
                return;
            }

            function createFieldLabel(text, inputElement, extraClassName) {
                const wrapper = document.createElement('label');
                wrapper.textContent = text;

                if (extraClassName) {
                    wrapper.className = extraClassName;
                }

                wrapper.appendChild(inputElement);
                return wrapper;
            }

            fields.forEach(function (field, index) {
                const fieldItem = document.createElement('article');
                fieldItem.className = 'form-builder-edit-modal__field';
                const labelInput = document.createElement('input');
                labelInput.type = 'text';
                labelInput.value = String(field.label || '');

                const nameInput = document.createElement('input');
                nameInput.type = 'text';
                nameInput.value = String(field.name || '');

                const typeSelect = document.createElement('select');
                [
                    ['text', 'Texto'],
                    ['email', 'Email'],
                    ['number', 'Numero'],
                    ['date', 'Data'],
                    ['textarea', 'Texto longo'],
                    ['select', 'Selecao'],
                ].forEach(function (typeOption) {
                    const option = document.createElement('option');
                    option.value = typeOption[0];
                    option.textContent = typeOption[1];
                    typeSelect.appendChild(option);
                });
                typeSelect.value = String(field.type || 'text');

                const placeholderInput = document.createElement('input');
                placeholderInput.type = 'text';
                placeholderInput.value = String(field.placeholder || '');

                const optionsInput = document.createElement('input');
                optionsInput.type = 'text';
                optionsInput.value = String(Array.isArray(field.options) ? field.options.join(', ') : '');

                const optionsClassName = 'form-builder-edit-modal__options' + (typeSelect.value === 'select' ? '' : ' is-hidden');
                const optionsWrapper = createFieldLabel('Opções (separadas por vírgula)', optionsInput, optionsClassName);

                fieldItem.appendChild(createFieldLabel('Rótulo', labelInput));
                fieldItem.appendChild(createFieldLabel('Identificador', nameInput));
                fieldItem.appendChild(createFieldLabel('Tipo', typeSelect));
                fieldItem.appendChild(createFieldLabel('Placeholder', placeholderInput));
                fieldItem.appendChild(optionsWrapper);

                if (typeSelect) {
                    typeSelect.addEventListener('change', function () {
                        if (!optionsWrapper) {
                            return;
                        }

                        optionsWrapper.classList.toggle('is-hidden', typeSelect.value !== 'select');
                    });
                }

                fieldItem.setAttribute('data-field-index', String(index));
                tabEditFields.appendChild(fieldItem);
            });
        }

        function closeTabEditModal() {
            if (!tabEditModal) {
                return;
            }

            tabEditModal.hidden = true;
        }

        function openTabEditModal(tabData) {
            if (!tabEditModal) {
                return;
            }

            if (tabEditName) {
                tabEditName.value = String((tabData && tabData.name) || '');
            }

            renderTabFields(tabData && tabData.fields ? tabData.fields : []);
            tabEditModal.hidden = false;
        }

        tabEditButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const payload = button.getAttribute('data-tab');

                if (!payload) {
                    return;
                }

                try {
                    openTabEditModal(JSON.parse(payload));
                } catch (error) {
                    openTabEditModal({ name: '', fields: [] });
                }
            });
        });

        tabEditCloseButtons.forEach(function (button) {
            button.addEventListener('click', closeTabEditModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && tabEditModal && tabEditModal.hidden === false) {
                closeTabEditModal();
            }
        });
    }());
</script>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>