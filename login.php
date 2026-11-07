<?php
// ============================================================
// login.php — Inicio de sesión (TP8)
// password_verify() contra el hash de la BD + session_regenerate_id(true)
// ============================================================
require_once __DIR__ . '/app/bootstrap.php'; // funciones, usuarios_db y conexión PDO (TP10)

$destino = fn (string $rol): string => $rol === ROL_ADMIN ? 'admin/index.php' : 'mi-cuenta.php';

if ($usuario = usuarioActual()) {
    header('Location: ' . $destino($usuario['rol']));
    exit;
}

$email = '';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mb_strtolower(limpiar($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrfValido($_POST['csrf'] ?? null)) {
        $error = 'La sesión del formulario expiró. Volvé a intentarlo.';
    } elseif ($email === '' || $password === '') {
        $error = 'Completá el email y la contraseña.';
    } else {
        $pdo = App\Core\Database::conexion();
        $usuario = autenticar($pdo, $email, $password);
        if ($usuario) {
            abrirSesion($usuario);
            header('Location: ' . $destino($usuario['rol']));
            exit;
        }
        // Mensaje genérico: no revela si el email existe
        $error = 'Email o contraseña incorrectos.';
    }
}

$avisos = [
    'acceso_denegado' => ['warning', 'Tenés que iniciar sesión para acceder a esa sección.'],
    'registro'        => ['success', 'Cuenta creada correctamente. Ya podés ingresar.'],
    'logout'          => ['info', 'Cerraste sesión correctamente.'],
];
$aviso = $avisos[filter_input(INPUT_GET, 'error') ?? filter_input(INPUT_GET, 'ok') ?? ''] ?? null;
$csrf  = csrfToken();

$titulo      = 'BOXLY | Ingresar';
$descripcion = 'Ingresá a tu cuenta de BOXLY para ver tus turnos.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Mi cuenta</p>
          <h1>Ingresar</h1>
          <p>Accedé para seguir el estado de tus turnos.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
              <?php if ($aviso): ?>
                <div class="alert alert-<?= e($aviso[0]) ?>" role="status"><?= e($aviso[1]) ?></div>
              <?php endif; ?>
              <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
              <?php endif; ?>

              <form class="bx-form bx-card p-4" method="post" action="login.php" novalidate>
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
                <div class="mb-3">
                  <label class="form-label" for="email">Email</label>
                  <input class="form-control" id="email" name="email" type="email" maxlength="150" required autocomplete="email" value="<?= e($email) ?>" />
                </div>
                <div class="mb-4">
                  <label class="form-label" for="password">Contraseña</label>
                  <input class="form-control" id="password" name="password" type="password" required autocomplete="current-password" />
                </div>
                <button class="btn btn-bx-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Ingresar</button>
                <p class="text-center small mt-3 mb-0">¿No tenés cuenta? <a href="registro.php">Registrate</a></p>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
