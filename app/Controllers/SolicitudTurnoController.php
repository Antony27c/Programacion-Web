<?php
// ============================================================
// SolicitudTurnoController — Solicitud pública de turno (turnos.php)
// Sanitización + validación en servidor (TP5), persistencia (TP7)
// y precarga de la cuenta logueada (TP8).
// ============================================================
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Catalogo;
use App\Models\Turno;
use App\Models\Usuario;

class SolicitudTurnoController extends Controller
{
    public function solicitar(): void
    {
        $catalogos = (new Catalogo())->todos();
        $datos     = Turno::vacio($catalogos);
        $errores   = [];
        $turnoId   = null;

        // Con sesión iniciada se precargan los datos y el turno queda a nombre de la cuenta
        $cuenta = ($u = usuarioActual()) ? (new Usuario())->obtener($u['id']) : null;
        if ($cuenta) {
            $datos = array_merge($datos, array_intersect_key($cuenta, array_flip(['nombre', 'apellido', 'email', 'telefono'])));
        }

        if ($this->esPost()) {
            // 1) Sanitizar y validar (listas blancas contra los catálogos)
            $entrada = $cuenta ? ['email' => $cuenta['email']] + $_POST : $_POST;
            [$datos, $errores] = Turno::validar($entrada, $catalogos);

            // 2) Guardar con consultas preparadas (anti SQL Injection)
            if ($errores === []) {
                $turnoId = (new Turno())->guardar($datos, false);
            }
        }

        $this->render('turnos/solicitar', [
            'titulo'      => 'BOXLY | Agendar turno',
            'descripcion' => 'Agendá tu turno en el taller BOXLY de Salta.',
            'catalogos'   => $catalogos,
            'horarios'    => Turno::HORARIOS,
            'datos'       => $datos,
            'errores'     => $errores,
            'turnoId'     => $turnoId,
            'exito'       => $turnoId !== null,
            'enviado'     => $this->esPost(),
            'emailFijo'   => $cuenta !== null,
            'admin'       => false,
        ]);
    }
}
