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
            'SELECT identificador_aba, nome_aba, campos
             FROM formularios_layout
             WHERE ativo = TRUE
             ORDER BY created_at ASC, id ASC'
        );

        $rows = $stmt->fetchAll() ?: [];
        $tabs = [];

        foreach ($rows as $row) {
            $camposRaw = $row['campos'] ?? '[]';
            $campos = json_decode(is_string($camposRaw) ? $camposRaw : '[]', true);

            if (!is_array($campos)) {
                $campos = [];
            }

            $tabs[] = [
                'id' => (string) ($row['identificador_aba'] ?? ''),
                'name' => (string) ($row['nome_aba'] ?? 'Aba personalizada'),
                'fields' => $this->normalizeFields($campos),
            ];
        }

        return $tabs;
    }

    /**
     * @param array<int, array{name:string,label:string,type:string,placeholder:string,options:array<int, string>}> $fields
     */
    public function addTab(string $name, array $fields, ?int $createdByUserId = null): void
    {
        $identifier = $this->buildUniqueIdentifier($name);
        $payload = json_encode($this->normalizeFields($fields), JSON_UNESCAPED_UNICODE);

        if (!is_string($payload)) {
            throw new RuntimeException('Nao foi possivel serializar os campos do formulario.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO formularios_layout (nome_aba, identificador_aba, campos, criado_por_usuario_id)
             VALUES (:nome_aba, :identificador_aba, CAST(:campos AS jsonb), :criado_por_usuario_id)'
        );

        $stmt->bindValue(':nome_aba', $name);
        $stmt->bindValue(':identificador_aba', $identifier);
        $stmt->bindValue(':campos', $payload);

        if ($createdByUserId === null) {
            $stmt->bindValue(':criado_por_usuario_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':criado_por_usuario_id', $createdByUserId, PDO::PARAM_INT);
        }

        $stmt->execute();
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
        $value = trim(mb_strtolower($value, 'UTF-8'));
        $value = preg_replace('/[^a-z0-9]+/u', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'aba-customizada';
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
}