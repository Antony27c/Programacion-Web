<?php
// ============================================================
// Controller — Clase base de los controladores (TP10)
// render(): arma la página con el layout existente
// (includes/header.php + vista + includes/footer.php).
// ============================================================
namespace App\Core;

abstract class Controller
{
    /**
     * Renderiza app/Views/{$vista}.php. Las claves de $datos quedan
     * disponibles como variables en el header, la vista y el footer.
     */
    protected function render(string $vista, array $datos = []): void
    {
        $archivo = BOXLY_RAIZ . '/app/Views/' . $vista . '.php';

        (static function (string $__archivo, array $__datos): void {
            extract($__datos, EXTR_SKIP);
            require BOXLY_RAIZ . '/includes/header.php';
            require $__archivo;
            require BOXLY_RAIZ . '/includes/footer.php';
        })($archivo, $datos);
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    protected function esPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
