<?php
// admin/eliminar.php — Front controller: Baja de turno (Delete), solo POST + CSRF → TurnoController::eliminar()
$rolesPermitidos = ['Administrador']; // TP8: solo administradores
require __DIR__ . '/../includes/auth_guard.php';
require __DIR__ . '/../app/bootstrap.php';

(new App\Controllers\TurnoController())->eliminar();
