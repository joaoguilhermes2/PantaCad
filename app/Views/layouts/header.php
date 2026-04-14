<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'PantaCad', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="Css/style.css">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? 'app-page', ENT_QUOTES, 'UTF-8'); ?>">
<script>
    (function () {
        try {
            if (window.localStorage.getItem('pantacad-dashboard-menu-collapsed') === 'true') {
                document.body.classList.add('dashboard-menu-collapsed');
            }
        } catch (error) {
        }
    }());
</script>
