<?php
// admin/editar.php — Front controller: Modificación de turno (Update) → TurnoController::editar()
$rolesPermitidos = ['Administrador']; // TP8: solo administradores
require __DIR__ . '/../includes/auth_guard.php';
require __DIR__ . '/../app/bootstrap.php';

(new App\Controllers\TurnoController())->editar();
