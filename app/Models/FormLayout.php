<?php

declare(strict_types=1);

final class FormLayout
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array<int, array{id:string,name:string,fields:array<int, array{name:string,label:string,type:string,placeholder:string,options:array<int, string>}>}>
     */
    public function listTabs(): array
    {
        $stmt = $this->pdo->query(
            'SELECT nome_aba, identificador_aba, rotulo, identificador, tipo, placeholder, opcoes
             FROM formularios_layout
             WHERE ativo = TRUE
             ORDER BY created_at ASC, id ASC'
        );

        $rows = $stmt->fetchAll() ?: [];
        $tabs = [];

        foreach ($rows as $row) {
            $tabIdentifier = (string) ($row['identificador_aba'] ?? '');

            if ($tabIdentifier === '') {
                continue;
            }

            if (!isset($tabs[$tabIdentifier])) {
                $tabs[$tabIdentifier] = [
                    'id' => $tabIdentifier,
                    'name' => (string) ($row['nome_aba'] ?? 'Aba personalizada'),
                    'fields' => [],
                ];
            }

            $tabs[$tabIdentifier]['fields'][] = $this->normalizeFieldRow($row);
        }

        return array_values($tabs);
    }

    /**
     * @param array<int, array{name:string,label:string,type:string,placeholder:string,options:array<int, string>}> $fields
     */
    public function addTab(string $name, array $fields, ?int $createdByUserId = null): void
    {
        $identifier = $this->buildUniqueIdentifier($name);
        $normalizedFields = $this->normalizeFields($fields);

        if ($normalizedFields === []) {
            throw new RuntimeException('Nao foi possivel preparar os campos do formulario.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO formularios_layout (
                nome_aba,
                identificador_aba,
                rotulo,
                identificador,
                tipo,
                placeholder,
                opcoes,
                criado_por_usuario_id
             ) VALUES (
                :nome_aba,
                :identificador_aba,
                :rotulo,
                :identificador,
                :tipo,
                :placeholder,
                CAST(:opcoes AS jsonb),
                :criado_por_usuario_id
             )'
        );

        $this->pdo->beginTransaction();

        try {
            foreach ($normalizedFields as $field) {
                $optionsPayload = json_encode($field['options'], JSON_UNESCAPED_UNICODE);

                if (!is_string($optionsPayload)) {
                    throw new RuntimeException('Nao foi possivel serializar as opcoes do campo.');
                }

                $stmt->bindValue(':nome_aba', $name);
                $stmt->bindValue(':identificador_aba', $identifier);
                $stmt->bindValue(':rotulo', $field['label']);
                $stmt->bindValue(':identificador', $field['name']);
                $stmt->bindValue(':tipo', $field['type']);
                $stmt->bindValue(':placeholder', $field['placeholder']);
                $stmt->bindValue(':opcoes', $optionsPayload);

                if ($createdByUserId === null) {
                    $stmt->bindValue(':criado_por_usuario_id', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':criado_por_usuario_id', $createdByUserId, PDO::PARAM_INT);
                }

                $stmt->execute();
            }

            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function deactivateTab(string $tabIdentifier): bool
    {
        $normalizedIdentifier = trim($this->toLower($tabIdentifier));

        if ($normalizedIdentifier === '') {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE formularios_layout
             SET ativo = FALSE
             WHERE LOWER(identificador_aba) = LOWER(:identificador_aba)
               AND ativo = TRUE'
        );

        $stmt->execute(['identificador_aba' => $normalizedIdentifier]);

        return $stmt->rowCount() > 0;
    }

    private function buildUniqueIdentifier(string $name): string
    {
        $base = $this->slugify($name);
        $candidate = $base;
        $index = 1;

        while ($this->identifierExists($candidate)) {
            $index++;
            $candidate = $base . '-' . $index;
        }

        return $candidate;
    }

    private function identifierExists(string $identifier): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1
             FROM formularios_layout
             WHERE LOWER(identificador_aba) = LOWER(:identificador_aba)
             LIMIT 1'
        );

        $stmt->execute(['identificador_aba' => $identifier]);

        return $stmt->fetchColumn() !== false;
    }

    private function slugify(string $value): string
    {
        $value = trim($this->toLower($value));
        $value = preg_replace('/[^a-z0-9]+/u', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'aba-customizada';
    }

    private function toLower(string $value): string
    {
        return function_exists('mb_strtolower')
            ? mb_strtolower($value, 'UTF-8')
            : strtolower($value);
    }

    /**
     * @param array<int, mixed> $fields
     * @return array<int, array{name:string,label:string,type:string,placeholder:string,options:array<int, string>}>
     */
    private function normalizeFields(array $fields): array
    {
        $normalized = [];

        foreach ($fields as $field) {
            if (!is_array($field)) {
                continue;
            }

            $name = trim((string) ($field['name'] ?? ''));
            $label = trim((string) ($field['label'] ?? ''));

            if ($name === '' || $label === '') {
                continue;
            }

            $options = [];

            if (is_array($field['options'] ?? null)) {
                $options = array_values(array_filter(
                    array_map(static fn (mixed $option): string => trim((string) $option), $field['options']),
                    static fn (string $option): bool => $option !== ''
                ));
            }

            $type = trim((string) ($field['type'] ?? 'text'));
            $allowedTypes = ['text', 'email', 'number', 'date', 'textarea', 'select'];

            if (!in_array($type, $allowedTypes, true)) {
                $type = 'text';
            }

            $normalized[] = [
                'name' => $name,
                'label' => $label,
                'type' => $type,
                'placeholder' => trim((string) ($field['placeholder'] ?? '')),
                'options' => $type === 'select' ? $options : [],
            ];
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $row
     * @return array{name:string,label:string,type:string,placeholder:string,options:array<int, string>}
     */
    private function normalizeFieldRow(array $row): array
    {
        $optionsRaw = $row['opcoes'] ?? '[]';
        $options = json_decode(is_string($optionsRaw) ? $optionsRaw : '[]', true);

        if (!is_array($options)) {
            $options = [];
        }

        $type = trim((string) ($row['tipo'] ?? 'text'));
        $allowedTypes = ['text', 'email', 'number', 'date', 'textarea', 'select'];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'text';
        }

        return [
            'name' => trim((string) ($row['identificador'] ?? '')),
            'label' => trim((string) ($row['rotulo'] ?? '')),
            'type' => $type,
            'placeholder' => trim((string) ($row['placeholder'] ?? '')),
            'options' => $type === 'select'
                ? array_values(array_filter(
                    array_map(static fn (mixed $option): string => trim((string) $option), $options),
                    static fn (string $option): bool => $option !== ''
                ))
                : [],
        ];
    }
}