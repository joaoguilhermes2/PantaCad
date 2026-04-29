<?php

$title = 'PantaCad | Usuarios';
$bodyClass = 'dashboard-page';
$defaultTabs = [];
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
                    <div class="users-page__intro-copy">
                        <h1>Cadastro de usuarios</h1>
                        <p>Preencha os dados do usuario por abas para organizar o cadastro.</p>
                        <div class="users-page__subgroups" aria-label="Subgrupos do usuario logado">
                            <?php if (($usuarioSubgrupos ?? []) !== []): ?>
                                <?php foreach ($usuarioSubgrupos as $subgrupo): ?>
                                    <span><?= htmlspecialchars((string) ($subgrupo['nome'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span>Sem subgrupo vinculado</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="users-page__identity-fields" aria-label="Dados de identificacao do parceiro">
                        <label for="usuario-codigo">
                            C&oacute;digo
                            <input id="usuario-codigo" name="codigo" type="text" form="users-form">
                        </label>
                        <label for="usuario-tipo">
                            Tipo
                            <select id="usuario-tipo" name="tipo" form="users-form">
                                <option value="">Selecione</option>
                                <option value="juridico">Jur&iacute;dico</option>
                                <option value="fisico">F&iacute;sico</option>
                            </select>
                        </label>
                        <label for="usuario-cpf">
                            CPF
                            <input id="usuario-cpf" name="cpf" type="text" inputmode="numeric" placeholder="000.000.000-00" form="users-form">
                        </label>
                        <label for="usuario-parceiro-desde">
                            Parceiro desde
                            <input id="usuario-parceiro-desde" name="parceiro_desde" type="date" form="users-form">
                        </label>
                    </div>
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

                    <form class="users-form" id="users-form" action="#" method="post" autocomplete="off">
                        <?php foreach ($tabs as $tabIndex => $tab): ?>
                            <section class="users-tab-panel <?= $tabIndex === 0 ? 'is-active' : ''; ?>" data-user-panel="<?= htmlspecialchars((string) $tab['id'], ENT_QUOTES, 'UTF-8'); ?>" role="tabpanel" <?= $tabIndex === 0 ? '' : 'hidden'; ?>>
                                <fieldset class="users-section">
                                    <legend><?= htmlspecialchars((string) $tab['name'], ENT_QUOTES, 'UTF-8'); ?></legend>
                                    <div class="users-form__grid <?= (string) $tab['id'] === 'endereco' ? 'users-form__grid--address' : ''; ?> <?= in_array((string) $tab['id'], ['dados-gerais', 'dados-cobranca', 'enderecos-entrega'], true) ? 'users-form__grid--general' : ''; ?>">
                                        <?php foreach (($tab['fields'] ?? []) as $field): ?>
                                            <?php
                                            $fieldName = (string) ($field['name'] ?? 'campo');
                                            $fieldId = 'usuario-' . (string) $tab['id'] . '-' . $fieldName;
                                            $fieldType = (string) ($field['type'] ?? 'text');
                                            $fieldWide = isset($field['wide']) ? max(1, min(4, (int) $field['wide'])) : 0;
                                            $fieldClasses = [];

                                            if ($fieldName === 'logradouro') {
                                                $fieldClasses[] = 'users-form__field--wide';
                                            }

                                            if ($fieldWide > 0) {
                                                $fieldClasses[] = 'users-form__field--span-' . $fieldWide;
                                            }
                                            ?>
                                            <?php if ($fieldType === 'checkbox_group'): ?>
                                                <fieldset class="users-form__choice-group <?= htmlspecialchars(implode(' ', $fieldClasses), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <legend><?= htmlspecialchars((string) ($field['label'] ?? 'Campo'), ENT_QUOTES, 'UTF-8'); ?></legend>
                                                    <div class="users-form__choices">
                                                        <?php foreach (($field['options'] ?? []) as $optionIndex => $option): ?>
                                                            <?php
                                                            $optionValue = (string) $option;
                                                            $optionId = $fieldId . '-' . $optionIndex;
                                                            $isChecked = in_array($optionValue, array_map('strval', is_array($field['default'] ?? null) ? $field['default'] : []), true);
                                                            ?>
                                                            <label class="users-form__choice" for="<?= htmlspecialchars($optionId, ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input
                                                                    id="<?= htmlspecialchars($optionId, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>[]"
                                                                    type="checkbox"
                                                                    value="<?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    <?= $isChecked ? 'checked' : ''; ?>
                                                                >
                                                                <span><?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8'); ?></span>
                                                            </label>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </fieldset>
                                            <?php elseif ($fieldType === 'checkbox'): ?>
                                                <label for="<?= htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>" class="users-form__check-field <?= htmlspecialchars(implode(' ', $fieldClasses), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input
                                                        id="<?= htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>"
                                                        name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>"
                                                        type="checkbox"
                                                        value="1"
                                                    >
                                                    <span><?= htmlspecialchars((string) ($field['label'] ?? 'Campo'), ENT_QUOTES, 'UTF-8'); ?></span>
                                                </label>
                                            <?php else: ?>
                                            <label for="<?= htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>" class="<?= htmlspecialchars(implode(' ', $fieldClasses), ENT_QUOTES, 'UTF-8'); ?>">
                                                <?= htmlspecialchars((string) ($field['label'] ?? 'Campo'), ENT_QUOTES, 'UTF-8'); ?>
                                                <?php if ($fieldType === 'textarea'): ?>
                                                    <textarea
                                                        id="<?= htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>"
                                                        name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>"
                                                        placeholder="<?= htmlspecialchars((string) ($field['placeholder'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                    ></textarea>
                                                <?php elseif ($fieldType === 'select' || $fieldType === 'radio'): ?>
                                                    <select id="<?= htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <option value="">Selecione</option>
                                                        <?php foreach (($field['options'] ?? []) as $option): ?>
                                                            <?php $optionValue = (string) $option; ?>
                                                            <option value="<?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8'); ?>" <?= (string) ($field['default'] ?? '') === $optionValue ? 'selected' : ''; ?>><?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8'); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input
                                                        id="<?= htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>"
                                                        name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8'); ?>"
                                                        type="<?= htmlspecialchars($fieldType, ENT_QUOTES, 'UTF-8'); ?>"
                                                        placeholder="<?= htmlspecialchars((string) ($field['placeholder'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                        <?= $fieldName === 'uf' ? 'maxlength="2"' : ''; ?>
                                                    >
                                                <?php endif; ?>
                                            </label>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </fieldset>
                            </section>
                        <?php endforeach; ?>
                    </form>

                    <div class="users-footer-tabs" aria-label="Se&ccedil;&otilde;es complementares do cadastro">
                        <div class="users-footer-tabs__row">
                            <button type="button" class="users-footer-tabs__button" data-open-client-modal>1 - Cliente</button>
                            <button type="button" class="users-footer-tabs__button" data-open-supplier-modal>2 - Fornecedor</button>
                            <button type="button" class="users-footer-tabs__button">3 - Transportador</button>
                            <button type="button" class="users-footer-tabs__button">4 - Propriedades</button>
                            <button type="button" class="users-footer-tabs__button">5 - Contatos</button>
                            <button type="button" class="users-footer-tabs__button">6 - V&iacute;nculos</button>
                            <button type="button" class="users-footer-tabs__button">7 - Dados Banc&aacute;rios</button>
                        </div>
                        <div class="users-footer-tabs__row">
                            <button type="button" class="users-footer-tabs__button">8 - Avalistas</button>
                            <button type="button" class="users-footer-tabs__button">9 - Contas Cont&aacute;beis</button>
                            <button type="button" class="users-footer-tabs__button">10 - Cooperado</button>
                            <button type="button" class="users-footer-tabs__button">11 - Anexo</button>
                            <button type="button" class="users-footer-tabs__button">12 - Assinatura digitalizada</button>
                            <button type="button" class="users-footer-tabs__button">14 - CNO</button>
                            <button type="button" class="users-footer-tabs__button">15 - Outros <span aria-hidden="true">▾</span></button>
                        </div>
                    </div>

                    <div class="supplier-modal client-modal" id="client-modal" role="dialog" aria-modal="true" aria-labelledby="client-modal-title" hidden>
                        <div class="supplier-modal__backdrop" data-close-client-modal></div>
                        <section class="supplier-modal__dialog client-modal__dialog">
                            <header class="supplier-modal__titlebar">
                                <h2 id="client-modal-title">Cadastro de cliente</h2>
                                <button type="button" class="supplier-modal__close" aria-label="Fechar" data-close-client-modal>&times;</button>
                            </header>

                            <div class="client-modal__tabs" aria-label="Abas do cliente">
                                <button type="button" class="client-modal__tab client-modal__tab--active" data-client-tab="registration">Ficha cadastral</button>
                                <button type="button" class="client-modal__tab" data-client-tab="personal">Dados pessoais</button>
                                <button type="button" class="client-modal__tab">Comercializa&ccedil;&atilde;o</button>
                                <button type="button" class="client-modal__tab">Cr&eacute;dito Rotativo</button>
                                <button type="button" class="client-modal__tab">Cr&eacute;dito por Ciclo</button>
                                <button type="button" class="client-modal__tab">Hist&oacute;rico financeiro inicial</button>
                                <button type="button" class="client-modal__tab">Hist&oacute;rico financeiro</button>
                            </div>

                            <div class="client-modal__body client-modal__panel client-modal__panel--active" data-client-panel="registration">
                                <label class="client-modal__field" for="client-registration">
                                    Matr&iacute;cula
                                    <input id="client-registration" type="text">
                                </label>

                                <label class="client-modal__field" for="client-agricultural-start">
                                    In&iacute;cio da atividade agr&iacute;cola
                                    <input id="client-agricultural-start" class="client-modal__date-input" type="date">
                                </label>

                                <fieldset class="client-modal__records">
                                    <legend>[Registros]</legend>

                                    <label class="client-modal__field" for="client-idaf">
                                        IDAF (ES)
                                        <input id="client-idaf" type="text">
                                    </label>

                                    <label class="client-modal__field client-modal__field--validity" for="client-idaf-validity">
                                        Validade
                                        <input id="client-idaf-validity" class="client-modal__date-input" type="date">
                                    </label>

                                    <label class="client-modal__field" for="client-ima">
                                        IMA (MG)
                                        <input id="client-ima" type="text">
                                    </label>

                                    <label class="client-modal__field" for="client-ima-record">
                                        Nr. registro IMA
                                        <input id="client-ima-record" type="text">
                                    </label>

                                    <label class="client-modal__field client-modal__field--validity" for="client-ima-validity">
                                        Validade
                                        <input id="client-ima-validity" class="client-modal__date-input" type="date">
                                    </label>

                                    <label class="client-modal__field" for="client-inea">
                                        INEA (RJ)
                                        <input id="client-inea" type="text">
                                    </label>

                                    <label class="client-modal__field client-modal__field--validity" for="client-inea-validity">
                                        Validade
                                        <input id="client-inea-validity" class="client-modal__date-input" type="date">
                                    </label>

                                    <label class="client-modal__field" for="client-cidasc">
                                        CIDASC (SC)
                                        <input id="client-cidasc" type="text">
                                    </label>

                                    <label class="client-modal__field client-modal__field--validity" for="client-cidasc-validity">
                                        Validade
                                        <input id="client-cidasc-validity" class="client-modal__date-input" type="date">
                                    </label>

                                    <label class="client-modal__field client-modal__field--rating" for="client-rating">
                                        Rating
                                        <input id="client-rating" type="text">
                                    </label>
                                </fieldset>
                            </div>

                            <div class="client-modal__body client-modal__panel client-personal-panel" data-client-panel="personal" hidden>
                                <div class="client-personal-panel__top-grid">
                                    <label class="client-personal-panel__field" for="client-personal-nationality">
                                        Nacionalidade
                                        <input id="client-personal-nationality" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-birthplace">
                                        Naturalidade
                                        <input id="client-personal-birthplace" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-birth-date">
                                        Nascimento
                                        <input id="client-personal-birth-date" type="date">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-marital-status">
                                        Estado Civil
                                        <select id="client-personal-marital-status">
                                            <option value="">Selecione</option>
                                            <option value="casado">Casado</option>
                                            <option value="solteiro">Solteiro</option>
                                            <option value="divorciado">Divorciado</option>
                                            <option value="viuvo">Vi&uacute;vo</option>
                                        </select>
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-marriage-regime">
                                        Regime casamento
                                        <input id="client-personal-marriage-regime" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-marriage-date">
                                        Casamento
                                        <input id="client-personal-marriage-date" type="date">
                                    </label>
                                    <label class="client-personal-panel__field client-personal-panel__field--wide" for="client-personal-occupation">
                                        Profiss&atilde;o
                                        <input id="client-personal-occupation" type="text">
                                    </label>
                                </div>

                                <fieldset class="client-personal-panel__fieldset">
                                    <legend>Filia&ccedil;&atilde;o</legend>
                                    <label class="client-personal-panel__field client-personal-panel__field--wide" for="client-personal-father-name">
                                        Nome do pai
                                        <input id="client-personal-father-name" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-father-birth-date">
                                        Nascimento
                                        <input id="client-personal-father-birth-date" type="date">
                                    </label>
                                    <label class="client-personal-panel__field client-personal-panel__field--wide" for="client-personal-mother-name">
                                        Nome da m&atilde;e
                                        <input id="client-personal-mother-name" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-mother-birth-date">
                                        Nascimento
                                        <input id="client-personal-mother-birth-date" type="date">
                                    </label>
                                </fieldset>

                                <fieldset class="client-personal-panel__fieldset">
                                    <legend>C&ocirc;njuge</legend>
                                    <label class="client-personal-panel__field client-personal-panel__field--wide" for="client-personal-spouse-name">
                                        Nome c&ocirc;njuge
                                        <input id="client-personal-spouse-name" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-spouse-birth-date">
                                        Nascimento
                                        <input id="client-personal-spouse-birth-date" type="date">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-spouse-nationality">
                                        Nacionalidade
                                        <input id="client-personal-spouse-nationality" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-spouse-birthplace">
                                        Naturalidade
                                        <input id="client-personal-spouse-birthplace" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-spouse-cpf">
                                        C.P.F
                                        <input id="client-personal-spouse-cpf" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-spouse-rg">
                                        RG
                                        <input id="client-personal-spouse-rg" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-spouse-issuer">
                                        &Oacute;rg&atilde;o expedidor
                                        <input id="client-personal-spouse-issuer" type="text">
                                    </label>
                                    <label class="client-personal-panel__field client-personal-panel__field--wide" for="client-personal-spouse-occupation">
                                        Profiss&atilde;o
                                        <input id="client-personal-spouse-occupation" type="text">
                                    </label>
                                </fieldset>

                                <fieldset class="client-personal-panel__fieldset">
                                    <legend>Endere&ccedil;o</legend>
                                    <label class="client-personal-panel__field client-personal-panel__field--address" for="client-personal-address">
                                        Endere&ccedil;o
                                        <input id="client-personal-address" type="text">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-address-number">
                                        N&uacute;mero
                                        <input id="client-personal-address-number" type="text">
                                    </label>
                                    <label class="client-personal-panel__field client-personal-panel__field--address" for="client-personal-neighborhood">
                                        Bairro
                                        <input id="client-personal-neighborhood" type="text">
                                    </label>
                                    <label class="client-personal-panel__field client-personal-panel__field--city" for="client-personal-city-code">
                                        Munic&iacute;pio
                                        <span class="client-personal-panel__lookup">
                                            <input id="client-personal-city-code" type="text">
                                            <button type="button" aria-label="Pesquisar municipio">⌕</button>
                                            <input type="text" aria-label="Nome do municipio">
                                        </span>
                                    </label>
                                    <label class="client-personal-panel__field client-personal-panel__field--uf" for="client-personal-address-uf">
                                        UF
                                        <input id="client-personal-address-uf" type="text" maxlength="2">
                                    </label>
                                    <label class="client-personal-panel__field" for="client-personal-address-cep">
                                        CEP
                                        <input id="client-personal-address-cep" type="text">
                                    </label>
                                </fieldset>

                                <fieldset class="client-personal-panel__fieldset client-personal-panel__fieldset--housing">
                                    <legend>Moradia</legend>
                                    <label class="client-personal-panel__radio">
                                        <input type="radio" name="client_personal_housing" value="propria">
                                        <span>Pr&oacute;pria - 1</span>
                                    </label>
                                    <label class="client-personal-panel__radio">
                                        <input type="radio" name="client_personal_housing" value="alugada">
                                        <span>Alugada - 2</span>
                                    </label>
                                    <label class="client-personal-panel__radio">
                                        <input type="radio" name="client_personal_housing" value="arrendada">
                                        <span>Arrendada - 3</span>
                                    </label>
                                    <label class="client-personal-panel__radio">
                                        <input type="radio" name="client_personal_housing" value="cedida">
                                        <span>Cedida - 4</span>
                                    </label>
                                    <label class="client-personal-panel__field client-personal-panel__field--time" for="client-personal-housing-time">
                                        Tempo
                                        <input id="client-personal-housing-time" type="text">
                                    </label>
                                    <span class="client-personal-panel__suffix">anos</span>
                                </fieldset>
                            </div>
                        </section>
                    </div>

                    <div class="supplier-modal" id="supplier-modal" role="dialog" aria-modal="true" aria-labelledby="supplier-modal-title" hidden>
                        <div class="supplier-modal__backdrop" data-close-supplier-modal></div>
                        <section class="supplier-modal__dialog">
                            <header class="supplier-modal__titlebar">
                                <h2 id="supplier-modal-title">Manuten&ccedil;&atilde;o do cadastro de parceiros</h2>
                                <button type="button" class="supplier-modal__close" aria-label="Fechar" data-close-supplier-modal>&times;</button>
                            </header>

                            <div class="supplier-modal__body">
                                <fieldset class="supplier-modal__section">
                                    <legend>Dados do representante</legend>

                                    <div class="supplier-modal__grid supplier-modal__grid--supplier">
                                        <label class="supplier-modal__field supplier-modal__field--name" for="supplier-name">
                                            Fornecedor
                                            <input id="supplier-name" type="text">
                                        </label>

                                        <label class="supplier-modal__field" for="supplier-category-code">
                                            Categoria
                                            <span class="supplier-modal__lookup">
                                                <input id="supplier-category-code" type="text">
                                                <button type="button" class="supplier-modal__lookup-button" aria-label="Pesquisar categoria">⌕</button>
                                                <input type="text" aria-label="Descricao da categoria">
                                            </span>
                                        </label>

                                        <label class="supplier-modal__field" for="supplier-commission">
                                            Banda de Comiss&atilde;o
                                            <span class="supplier-modal__lookup">
                                                <input id="supplier-commission" type="text">
                                                <button type="button" class="supplier-modal__lookup-button" aria-label="Pesquisar banda de comissao">⌕</button>
                                                <input type="text" aria-label="Descricao da banda de comissao">
                                            </span>
                                        </label>
                                    </div>

                                    <div class="supplier-modal__options-panel">
                                        <label class="supplier-modal__check supplier-modal__check--active">
                                            <input type="checkbox">
                                            <span>Distribuidor de Produtos de Marca</span>
                                        </label>
                                        <label class="supplier-modal__field supplier-modal__field--brand" for="supplier-brand">
                                            Marca
                                            <input id="supplier-brand" type="text">
                                        </label>
                                    </div>

                                    <label class="supplier-modal__check supplier-modal__check--active">
                                        <input type="checkbox">
                                        <span>Exporta pedido de compra</span>
                                    </label>

                                    <label class="supplier-modal__field" for="supplier-personal-data">
                                        Dados de Pessoal
                                        <span class="supplier-modal__lookup">
                                            <input id="supplier-personal-data" type="text">
                                            <button type="button" class="supplier-modal__lookup-button" aria-label="Pesquisar dados de pessoal">⌕</button>
                                            <input type="text" aria-label="Descricao dos dados de pessoal">
                                        </span>
                                    </label>

                                    <label class="supplier-modal__check supplier-modal__check--active">
                                        <input type="checkbox">
                                        <span>Incluir Parceiros Bloqueados para este Fornecedor</span>
                                    </label>
                                </fieldset>

                                <div class="supplier-modal__tabs" aria-label="Abas do fornecedor">
                                    <button type="button">Parceiros Bloqueados</button>
                                    <button type="button">Categorias Bloqueadas</button>
                                    <button type="button">Tipos de pedidos</button>
                                    <button type="button">Limite de Cr&eacute;dito</button>
                                </div>

                                <div class="supplier-modal__actions">
                                    <button type="button" class="supplier-modal__action supplier-modal__action--edit">Alterar</button>
                                    <button type="button" class="supplier-modal__action supplier-modal__action--delete">Excluir</button>
                                    <button type="button" class="supplier-modal__action supplier-modal__action--disabled" disabled>Gravar</button>
                                    <button type="button" class="supplier-modal__action supplier-modal__action--exit" data-close-supplier-modal>Sair</button>
                                </div>
                            </div>
                        </section>
                    </div>
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
        const clientModal = document.getElementById('client-modal');
        const clientOpenButton = document.querySelector('[data-open-client-modal]');
        const clientCloseButtons = Array.from(document.querySelectorAll('[data-close-client-modal]'));
        const clientTabButtons = Array.from(document.querySelectorAll('[data-client-tab]'));
        const clientPanels = Array.from(document.querySelectorAll('[data-client-panel]'));
        const supplierModal = document.getElementById('supplier-modal');
        const supplierOpenButton = document.querySelector('[data-open-supplier-modal]');
        const supplierCloseButtons = Array.from(document.querySelectorAll('[data-close-supplier-modal]'));

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

        clientTabButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const target = button.getAttribute('data-client-tab');

                if (!target) {
                    return;
                }

                clientTabButtons.forEach(function (item) {
                    item.classList.toggle('client-modal__tab--active', item === button);
                });

                clientPanels.forEach(function (panel) {
                    const isActive = panel.getAttribute('data-client-panel') === target;
                    panel.classList.toggle('client-modal__panel--active', isActive);
                    panel.hidden = !isActive;
                });
            });
        });

        function closeClientModal() {
            if (!clientModal) {
                return;
            }

            clientModal.hidden = true;
            body.classList.remove('modal-open');

            if (clientOpenButton) {
                clientOpenButton.blur();
            }
        }

        function openClientModal() {
            if (!clientModal) {
                return;
            }

            clientModal.hidden = false;
            body.classList.add('modal-open');

            const closeButton = clientModal.querySelector('[data-close-client-modal]');

            if (closeButton) {
                closeButton.focus();
            }
        }

        function closeSupplierModal() {
            if (!supplierModal) {
                return;
            }

            supplierModal.hidden = true;
            body.classList.remove('modal-open');

            if (supplierOpenButton) {
                supplierOpenButton.blur();
            }
        }

        function openSupplierModal() {
            if (!supplierModal) {
                return;
            }

            supplierModal.hidden = false;
            body.classList.add('modal-open');

            const closeButton = supplierModal.querySelector('[data-close-supplier-modal]');

            if (closeButton) {
                closeButton.focus();
            }
        }

        if (clientOpenButton) {
            clientOpenButton.addEventListener('click', openClientModal);
        }

        clientCloseButtons.forEach(function (button) {
            button.addEventListener('click', closeClientModal);
        });

        if (supplierOpenButton) {
            supplierOpenButton.addEventListener('click', openSupplierModal);
        }

        supplierCloseButtons.forEach(function (button) {
            button.addEventListener('click', closeSupplierModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            if (supplierModal && !supplierModal.hidden) {
                closeSupplierModal();
                return;
            }

            if (clientModal && !clientModal.hidden) {
                closeClientModal();
            }
        });
    }());
</script>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
