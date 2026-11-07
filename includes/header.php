<?php
// header.php — <head> + navegación compartida (include común)
// Variables esperadas: $titulo, $descripcion (definidas por cada página)
// Opcionales: $base (prefijo de rutas, '../' desde admin/) y $seccion
require_once __DIR__ . '/funciones.php';

$base       = $base ?? '';
$pagina     = $seccion ?? basename($_SERVER['PHP_SELF']);
$usuarioNav = usuarioActual();
$nav = function (string $archivo) use ($pagina): string {
    return $archivo === $pagina ? ' active' : '';
};
// aria-current para que el lector de pantalla anuncie la página actual
$actual = function (string $archivo) use ($pagina): string {
    return $archivo === $pagina ? ' aria-current="page"' : '';
};
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="<?= e($descripcion ?? 'BOXLY — Taller de servicio automotor en Salta.') ?>" />
    <meta name="theme-color" content="#0f172a" />
    <title><?= e($titulo ?? 'BOXLY') ?></title>
    <link rel="icon" href="<?= e($base) ?>img/favicon.svg" type="image/svg+xml" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= e($base) ?>css/styles.css" />
    <?php foreach ($estilos ?? [] as $estilo): // hojas de estilo propias de cada sección ?>
    <link rel="stylesheet" href="<?= e($base . $estilo) ?>" />
    <?php endforeach; ?>
    <script>document.documentElement.classList.add('js');</script>
  </head>
  <body>
    <a class="skip-link" href="#contenido">Saltar al contenido</a>
    <div class="bx-topbar d-none d-md-block">
      <div class="container d-flex justify-content-between align-items-center gap-3">
        <span><i class="bi bi-clock" aria-hidden="true"></i> Lun a Vie 8:00–18:00 · Sáb 8:00–13:00</span>
        <span class="d-flex gap-4">
          <a href="tel:+543875550199"><i class="bi bi-telephone" aria-hidden="true"></i> +54 387 555-0199</a>
          <a href="https://wa.me/5493875550199" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp</a>
        </span>
      </div>
    </div>
    <header class="sticky-top bx-header">
      <nav class="navbar navbar-expand-lg bx-navbar" aria-label="Principal">
        <div class="container">
          <a class="navbar-brand" href="<?= e($base) ?>index.php">
            <span class="bx-logo-mark" aria-hidden="true">BX</span>
            <span>BOXLY</span>
          </a>
          <div class="d-flex align-items-center gap-2 d-lg-none">
            <a class="btn btn-bx-primary btn-sm" href="<?= e($base) ?>turnos.php">Sacar turno</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
              <span class="navbar-toggler-icon"></span>
            </button>
          </div>
          <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
              <li class="nav-item"><a class="nav-link<?= $nav('index.php') ?>"<?= $actual('index.php') ?> href="<?= e($base) ?>index.php">Inicio</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('nosotros.php') ?>"<?= $actual('nosotros.php') ?> href="<?= e($base) ?>nosotros.php">Nosotros</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('servicios.php') ?>"<?= $actual('servicios.php') ?> href="<?= e($base) ?>servicios.php">Servicios</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('turnos.php') ?>"<?= $actual('turnos.php') ?> href="<?= e($base) ?>turnos.php">Turnos</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('galeria.php') ?>"<?= $actual('galeria.php') ?> href="<?= e($base) ?>galeria.php">Galería</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('contacto.php') ?>"<?= $actual('contacto.php') ?> href="<?= e($base) ?>contacto.php">Contacto</a></li>
              <?php if ($usuarioNav): ?>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle<?= $nav('mi-cuenta.php') . $nav('admin') ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle" aria-hidden="true"></i> <?= e($usuarioNav['nombre']) ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= e($base) ?>mi-cuenta.php"><i class="bi bi-person me-2" aria-hidden="true"></i>Mi cuenta</a></li>
                    <?php if ($usuarioNav['rol'] === 'Administrador'): ?>
                      <li><a class="dropdown-item" href="<?= e($base) ?>admin/index.php"><i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>Panel de turnos</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                      <form class="m-0" method="post" action="<?= e($base) ?>logout.php">
                        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>" />
                        <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Cerrar sesión</button>
                      </form>
                    </li>
                  </ul>
                </li>
              <?php else: ?>
                <li class="nav-item"><a class="nav-link<?= $nav('login.php') ?>"<?= $actual('login.php') ?> href="<?= e($base) ?>login.php"><i class="bi bi-person-circle" aria-hidden="true"></i> Ingresar</a></li>
              <?php endif; ?>
              <li class="nav-item d-none d-lg-block"><a class="btn btn-bx-primary ms-lg-2" href="<?= e($base) ?>turnos.php"><i class="bi bi-calendar-check" aria-hidden="true"></i> Sacar turno</a></li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
