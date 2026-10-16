<?php
// header.php — <head> + navegación compartida (include común)
// Variables esperadas: $titulo, $descripcion (definidas por cada página)
require_once __DIR__ . '/funciones.php';

$pagina = basename($_SERVER['PHP_SELF']);
$nav = function (string $archivo) use ($pagina): string {
    return $archivo === $pagina ? ' active' : '';
};
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="<?= e($descripcion ?? 'BOXLY — Taller de servicio automotor en Salta.') ?>" />
    <title><?= e($titulo ?? 'BOXLY') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <a class="skip-link" href="#contenido">Saltar al contenido</a>
    <header>
      <nav class="navbar navbar-expand-lg bx-navbar" aria-label="Principal">
        <div class="container">
          <a class="navbar-brand" href="index.php">
            <span class="bx-logo-mark" aria-hidden="true">BX</span>
            <span>BOXLY</span>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
              <li class="nav-item"><a class="nav-link<?= $nav('index.php') ?>" href="index.php">Inicio</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('nosotros.php') ?>" href="nosotros.php">Nosotros</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('servicios.php') ?>" href="servicios.php">Servicios</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('turnos.php') ?>" href="turnos.php">Turnos</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('galeria.php') ?>" href="galeria.php">Galería</a></li>
              <li class="nav-item"><a class="nav-link<?= $nav('contacto.php') ?>" href="contacto.php">Contacto</a></li>
              <li class="nav-item"><a class="btn btn-bx-primary ms-lg-2" href="turnos.php">Agendar turno</a></li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
