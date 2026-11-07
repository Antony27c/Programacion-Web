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
    // is_scalar: un password[]=x llegaría como array y rompería el casteo
    $password = is_scalar($_POST['password'] ?? null) ? (string) $_POST['password'] : '';

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
$estilos     = ['css/auth.css'];
$scripts     = ['js/auth.js'];
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-auth">
        <div class="container">
          <div class="bx-auth-shell">
            <aside class="bx-auth-aside" aria-label="Beneficios de tener una cuenta">
              <p class="eyebrow">Tu cuenta BOXLY</p>
              <h2>Seguí tu auto sin llamar al taller</h2>
              <p>Con tu cuenta ves en qué estado está cada turno y tus datos quedan cargados para el próximo.</p>
              <figure class="bx-auth-media">
                <img src="img/hero-taller.svg" width="640" height="520" alt="Ilustración: auto revisado en el taller con tarjetas de turno confirmado y presupuesto aprobado" />
              </figure>
              <ul class="bx-auth-benefits">
                <li><i class="bi bi-calendar-check" aria-hidden="true"></i> Estado de tus turnos: pendiente, confirmado o finalizado.</li>
                <li><i class="bi bi-lightning-charge" aria-hidden="true"></i> Pedís un turno nuevo con tus datos ya completos.</li>
                <li><i class="bi bi-shield-lock" aria-hidden="true"></i> Contraseña cifrada y sesión que se cierra sola por inactividad.</li>
              </ul>
            </aside>

            <div class="bx-auth-card">
              <span class="bx-logo-mark bx-auth-mark" aria-hidden="true">BX</span>
              <h1>Ingresar</h1>
              <p class="bx-lead">Accedé para seguir el estado de tus turnos.</p>

              <?php if ($aviso): ?>
                <div class="alert alert-<?= e($aviso[0]) ?>" role="<?= $aviso[0] === 'success' || $aviso[0] === 'info' ? 'status' : 'alert' ?>"><?= e($aviso[1]) ?></div>
              <?php endif; ?>
              <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
              <?php endif; ?>

              <form class="bx-form" method="post" action="login.php" novalidate>
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
                <div class="mb-3">
                  <label class="form-label" for="email">Email</label>
                  <input class="form-control" id="email" name="email" type="email" maxlength="150" required autocomplete="email" placeholder="nombre@correo.com" value="<?= e($email) ?>" />
                </div>
                <div class="mb-4">
                  <label class="form-label" for="password">Contraseña</label>
                  <div class="bx-password">
                    <input class="form-control" id="password" name="password" type="password" required autocomplete="current-password" />
                    <button class="bx-password-toggle" type="button" data-toggle-password aria-controls="password" aria-pressed="false" aria-label="Mostrar contraseña">
                      <i class="bi bi-eye" aria-hidden="true"></i>
                    </button>
                  </div>
                </div>
                <button class="btn btn-bx-primary btn-lg w-100" type="submit"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Ingresar</button>
              </form>
              <p class="bx-auth-alt">¿No tenés cuenta? <a href="registro.php">Creá una gratis</a></p>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
