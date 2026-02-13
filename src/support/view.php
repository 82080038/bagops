<?php
function view(string $template, array $data = []): string
{
    // templates berada di root /templates, bukan di /src/templates
    $file = __DIR__ . '/../../templates/' . $template . '.php';
    if (!file_exists($file)) {
        return 'Template not found';
    }
    extract($data, EXTR_SKIP);
    ob_start();
    include $file;
    return ob_get_clean();
}
