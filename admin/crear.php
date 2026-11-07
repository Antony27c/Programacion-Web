<?php
// admin/crear.php — Front controller: Alta de turno (Create) → TurnoController::crear()
$rolesPermitidos = ['Administrador']; // TP8: solo administradores
require __DIR__ . '/../includes/auth_guard.php';
require __DIR__ . '/../app/bootstrap.php';

(new App\Controllers\TurnoController())->crear();
