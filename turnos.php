<?php
// turnos.php — Front controller: solicitud pública de turno → SolicitudTurnoController::solicitar()
require __DIR__ . '/app/bootstrap.php';

(new App\Controllers\SolicitudTurnoController())->solicitar();
