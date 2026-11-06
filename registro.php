<?php
// ============================================================
// registro.php — Alta de cuenta de cliente (TP8)
// Sanitización + validación en servidor, email único y
// password_hash(PASSWORD_BCRYPT) con consulta preparada
// ============================================================
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/usuarios_db.php';

if (usuarioActual()) {
    header('Location: mi-cuenta.php');
    exit;
}

$datos   = array_fill_keys(['nombre', 'apellido', 'email', 'telefono'], '');
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$datos, $errores] = validarRegistro($_POST);

    if (!csrfValido($_POST['csrf'] ?? null)) {
        $errores['csrf'] = 'La sesión del formulario expiró. Volvé a intentarlo.';
    }

    if ($errores === []) {
        require_once __DIR__ . '/config/conexion.php';
        try {
            if (emailRegistrado($pdo, $datos['email'])) {
                $errores['email'] = 'Ya existe una cuenta con ese email.';
            } else {
                registrarUsuario($pdo, $datos);
                header('Location: login.php?ok=registro');
                exit;
            }
        } catch (PDOException $e) {
            // 23000: otro registro con el mismo email se insertó al mismo tiempo (UNIQUE)
            if ($e->getCode() !== '23000') {
                throw $e;
            }
            $errores['email'] = 'Ya existe una cuenta con ese email.';
        }
    }
    unset($datos['password']);
}

$csrf = csrfToken();

$titulo      = 'BOXLY | Crear cuenta';
$descripcion = 'Creá tu cuenta en BOXLY para seguir tus turnos.';
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-page-hero">
        <div class="container">
          <p class="eyebrow">Mi cuenta</p>
          <h1>Crear cuenta</h1>
          <p>Registrate para ver el historial y el estado de tus turnos.</p>
        </div>
      </section>
      <section class="bx-section bx-section-white">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <?php if ($errores): ?>
                <div class="alert alert-danger" role="alert">
                  <strong>Revisá el formulario:</strong> <?= e($errores['csrf'] ?? 'encontramos ' . count($errores) . ' error(es) de validación.') ?>
                </div>
              <?php endif; ?>

              <form class="bx-form bx-card p-4" method="post" action="registro.php" novalidate>
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input class="form-control<?= claseError($errores, 'nombre') ?>" id="nombre" name="nombre" maxlength="60" required autocomplete="given-name" value="<?= valor($datos, 'nombre') ?>" />
                    <?= errorCampo($errores, 'nombre') ?>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="apellido">Apellido</label>
                    <input class="form-control<?= claseError($errores, 'apellido') ?>" id="apellido" name="apellido" maxlength="60" required autocomplete="family-name" value="<?= valor($datos, 'apellido') ?>" />
                    <?= errorCampo($errores, 'apellido') ?>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control<?= claseError($errores, 'email') ?>" id="email" name="email" type="email" maxlength="150" required autocomplete="email" value="<?= valor($datos, 'email') ?>" />
                    <?= errorCampo($errores, 'email') ?>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="telefono">Teléfono / WhatsApp</label>
                    <input class="form-control<?= claseError($errores, 'telefono') ?>" id="telefono" name="telefono" type="tel" maxlength="20" required autocomplete="tel" value="<?= valor($datos, 'telefono') ?>" />
                    <?= errorCampo($errores, 'telefono') ?>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="password">Contraseña</label>
                    <input class="form-control<?= claseError($errores, 'password') ?>" id="password" name="password" type="password" minlength="8" maxlength="72" required autocomplete="new-password" aria-describedby="ayudaPassword" />
                    <?= errorCampo($errores, 'password') ?>
                    <div class="form-text" id="ayudaPassword">Mínimo 8 caracteres, con letras y números.</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="password2">Repetir contraseña</label>
                    <input class="form-control<?= claseError($errores, 'password2') ?>" id="password2" name="password2" type="password" maxlength="72" required autocomplete="new-password" />
                    <?= errorCampo($errores, 'password2') ?>
                  </div>
                </div>
                <button class="btn btn-bx-primary mt-4" type="submit"><i class="bi bi-person-plus" aria-hidden="true"></i> Crear cuenta</button>
                <p class="small mt-3 mb-0">¿Ya tenés cuenta? <a href="login.php">Ingresá</a></p>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
