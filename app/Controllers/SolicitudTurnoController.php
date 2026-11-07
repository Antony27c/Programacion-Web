<?php
// ============================================================
// SolicitudTurnoController — Solicitud pública de turno (turnos.php)
// Sanitización + validación en servidor (TP5), persistencia (TP7)
// y precarga de la cuenta logueada (TP8).
// TP9: panel «Consultá tu turno» (número + patente); con JS lo resuelve
//      js/turnos.js vía api/consultar_turno.php y sin JS se resuelve acá.
// ============================================================
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Catalogo;
use App\Models\Turno;
use App\Models\Usuario;
use PDOException;

class SolicitudTurnoController extends Controller
{
    public function solicitar(): void
    {
        $catalogos = (new Catalogo())->todos();
        $datos     = Turno::vacio($catalogos);
        $errores   = [];
        $turnoId   = null;
        $errorGeneral = null;

        // Con sesión iniciada se precargan los datos y el turno queda a nombre de la cuenta
        $cuenta = ($u = usuarioActual()) ? (new Usuario())->obtener($u['id']) : null;
        if ($cuenta) {
            $datos = array_merge($datos, array_intersect_key($cuenta, array_flip(['nombre', 'apellido', 'email', 'telefono'])));
        }

        if ($this->esPost()) {
            if (!csrfValido($_POST['csrf'] ?? null)) {
                $errorGeneral = 'El formulario expiró o la solicitud no es válida. Recargá la página e intentá nuevamente.';
            } else {
                // 1) Sanitizar y validar (listas blancas contra los catálogos)
                $entrada = $cuenta ? ['email' => $cuenta['email']] + $_POST : $_POST;
                [$datos, $errores] = Turno::validar($entrada, $catalogos);

                // 2) Guardar con consultas preparadas (anti SQL Injection)
                if ($errores === []) {
                    try {
                        $turnoId = (new Turno())->guardar($datos, false);
                    } catch (PDOException $ex) {
                        // Error controlado: el detalle queda en el log del servidor
                        error_log('Error al guardar turno desde turnos.php: ' . $ex->getMessage());
                        $errorGeneral = 'No se pudo registrar el turno en la base de datos. Intentá nuevamente en unos minutos.';
                    }
                }
            }
        }

        // TP9: consulta de estado sin JS (mismas validaciones que api/consultar_turno.php)
        $consultaId      = limpiar(filter_input(INPUT_GET, 'consulta_id', FILTER_UNSAFE_RAW));
        $consultaPatente = limpiar(filter_input(INPUT_GET, 'patente', FILTER_UNSAFE_RAW));
        $consulta        = null;
        $consultaError   = null;
        if ($consultaId !== '' || $consultaPatente !== '') {
            $idValido      = ctype_digit($consultaId) && (int) $consultaId > 0 ? (int) $consultaId : null;
            $patenteValida = Turno::normalizarPatente($consultaPatente);
            if ($idValido === null) {
                $consultaError = 'Ingresá un número de turno válido (entero positivo).';
            } elseif ($patenteValida === null) {
                $consultaError = 'Ingresá una patente válida (ABC123 o AB123CD).';
            } else {
                $consulta = (new Turno())->consultarEstado($idValido, $patenteValida);
                if ($consulta === null) {
                    $consultaError = "No encontramos el turno N° $idValido para la patente $patenteValida.";
                }
            }
        }

        $this->render('turnos/solicitar', [
            'titulo'      => 'BOXLY | Agendar turno',
            'descripcion' => 'Agendá tu turno en el taller BOXLY de Salta.',
            'catalogos'   => $catalogos,
            'horarios'    => Turno::HORARIOS,
            'datos'       => $datos,
            'errores'     => $errores,
            'errorGeneral' => $errorGeneral,
            'csrf'        => csrfToken(),
            'turnoId'     => $turnoId,
            'exito'       => $turnoId !== null,
            'enviado'     => $this->esPost(),
            'emailFijo'   => $cuenta !== null,
            'admin'       => false,
            'consultaId'      => $consultaId,
            'consultaPatente' => $consultaPatente,
            'consulta'        => $consulta,
            'consultaError'   => $consultaError,
            'scripts'         => ['js/turnos.js'],
        ]);
    }
}
