<?php
// admin/index.php — Front controller: Listado de turnos (Read) → TurnoController::index()
$rolesPermitidos = ['Administrador']; // TP8: solo administradores
require __DIR__ . '/../includes/auth_guard.php';
require __DIR__ . '/../app/bootstrap.php';

(new App\Controllers\TurnoController())->index();
