<?php
// ============================================================
// TurnoController — CRUD de turnos del panel de administración
// Front controllers: admin/index.php, crear.php, editar.php, eliminar.php
// (el guard de rol Administrador se aplica antes, en cada punto de entrada).
// ============================================================
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Catalogo;
use App\Models\Turno;
use PDOException;

class TurnoController extends Controller
{
    private const MENSAJES = [
        'created'  => ['success', 'Turno creado correctamente.'],
        'updated'  => ['success', 'Turno actualizado correctamente.'],
        'deleted'  => ['success', 'Turno eliminado correctamente.'],
        'dberror'  => ['danger', 'No se pudo eliminar el turno en la base de datos. Intentá nuevamente.'],
        'notfound' => ['warning', 'El turno solicitado no existe.'],
        'invalid'  => ['danger', 'La solicitud no es válida. Volvé a intentarlo.'],
    ];

    private Turno $turnos;
    private array $catalogos;

    public function __construct()
    {
        $this->turnos    = new Turno();
        $this->catalogos = (new Catalogo())->todos();
    }

    /**
     * Listado (Read) con filtro opcional por estado.
     */
    public function index(): void
    {
        // Lista blanca contra el catálogo + consulta preparada en el modelo
        $filtro = limpiar(filter_input(INPUT_GET, 'estado') ?? '');
        if (!enCatalogo($filtro, $this->catalogos['estados'])) {
            $filtro = '';
        }

        $this->render('turnos/index', $this->layout([
            'titulo'      => 'BOXLY | Administración de turnos',
            'descripcion' => 'Panel de gestión de turnos del taller BOXLY.',
            'turnos'      => $this->turnos->listar((int) $filtro),
            'filtro'      => $filtro,
            'status'      => self::MENSAJES[filter_input(INPUT_GET, 'status') ?? ''] ?? null,
        ]));
    }

    /**
     * Alta (Create).
     */
    public function crear(): void
    {
        $datos   = Turno::vacio($this->catalogos);
        $errores = [];
        $errorDb = null;

        if ($this->esPost()) {
            $this->exigirCsrf();
            [$datos, $errores] = Turno::validar($_POST, $this->catalogos, true);

            if ($errores === []) {
                $errorDb = $this->guardar($datos, 'created');
            }
        }

        $this->render('turnos/form', $this->layout([
            'titulo'      => 'BOXLY | Nuevo turno',
            'descripcion' => 'Alta de turno en el panel de administración.',
            'encabezado'  => 'Nuevo turno',
            'bajada'      => 'Cargá los datos del cliente y del vehículo.',
            'accion'      => 'crear.php',
            'textoBoton'  => 'Guardar turno',
            'datos'       => $datos,
            'errores'     => $errores,
            'errorDb'     => $errorDb,
        ]));
    }

    /**
     * Modificación (Update).
     */
    public function editar(): void
    {
        $id = filter_input($this->esPost() ? INPUT_POST : INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        // Lectura segura del registro a editar (JOIN con usuarios y vehiculos)
        $turno = $id ? $this->turnos->obtener($id) : null;
        if (!$turno) {
            $this->redirect('index.php?status=notfound');
        }

        $datos   = $turno;
        $errores = [];
        $errorDb = null;

        if ($this->esPost()) {
            $this->exigirCsrf();
            [$datos, $errores] = Turno::validar($_POST, $this->catalogos, true);

            if ($errores === []) {
                $errorDb = $this->guardar($datos, 'updated', $id);
            }
        }

        $this->render('turnos/form', $this->layout([
            'titulo'      => 'BOXLY | Editar turno #' . $id,
            'descripcion' => 'Edición de turno en el panel de administración.',
            'encabezado'  => 'Editar turno #' . $id,
            'bajada'      => 'Modificá los datos o actualizá el estado del turno.',
            'accion'      => 'editar.php',
            'textoBoton'  => 'Guardar cambios',
            'id'          => $id,
            'datos'       => $datos,
            'errores'     => $errores,
            'errorDb'     => $errorDb,
        ]));
    }

    /**
     * Baja (Delete). Solo POST con token CSRF: un link GET no puede borrar datos.
     */
    public function eliminar(): void
    {
        if (!$this->esPost()) {
            $this->redirect('index.php?status=invalid');
        }
        $this->exigirCsrf();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $status = 'notfound';
        if ($id) {
            try {
                $status = $this->turnos->eliminar($id) ? 'deleted' : 'notfound';
            } catch (PDOException $ex) {
                // Error controlado: el detalle queda en el log del servidor
                error_log('Error al eliminar turno: ' . $ex->getMessage());
                $status = 'dberror';
            }
        }

        $this->redirect('index.php?status=' . $status);
    }

    /**
     * Guarda el turno y redirige con $status. Si la base falla devuelve el
     * mensaje para la vista (el detalle técnico queda en el log del servidor).
     */
    private function guardar(array $datos, string $status, ?int $id = null): string
    {
        try {
            $this->turnos->guardar($datos, true, $id);
        } catch (PDOException $ex) {
            error_log('Error al guardar turno: ' . $ex->getMessage());
            return 'No se pudo guardar el turno en la base de datos. Intentá nuevamente.';
        }
        $this->redirect('index.php?status=' . $status);
    }

    private function exigirCsrf(): void
    {
        if (!csrfValido($_POST['csrf'] ?? null)) {
            $this->redirect('index.php?status=invalid');
        }
    }

    /**
     * Variables comunes a todas las vistas del panel.
     */
    private function layout(array $datos): array
    {
        return $datos + [
            'base'      => '../',
            'seccion'   => 'admin',
            'csrf'      => csrfToken(),
            'catalogos' => $this->catalogos,
            'horarios'  => Turno::HORARIOS,
            'admin'     => true,
        ];
    }
}
