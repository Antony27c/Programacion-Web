<?php
// ============================================================
// registro.php — Alta de cuenta de cliente (TP8)
// Sanitización + validación en servidor, email único y
// password_hash(PASSWORD_BCRYPT) con consulta preparada
// ============================================================
require_once __DIR__ . '/app/bootstrap.php'; // funciones, usuarios_db y conexión PDO (TP10)

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
        $pdo = App\Core\Database::conexion();
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
$estilos     = ['css/auth.css'];
$scripts     = ['js/auth.js'];
require __DIR__ . '/includes/header.php';
?>
    <main id="contenido">
      <section class="bx-auth">
        <div class="container">
          <div class="bx-auth-shell bx-auth-shell--ancho">
            <aside class="bx-auth-aside" aria-label="Beneficios de crear una cuenta">
              <p class="eyebrow">Gratis y en un minuto</p>
              <h2>Tu historial del taller, siempre a mano</h2>
              <p>Registrate una vez y después sacás turnos sin volver a cargar tus datos.</p>
              <figure class="bx-auth-media">
                <img src="img/ilustraciones/auto-entregado.svg" width="480" height="320" alt="Ilustración: auto entregado con sello de verificación" />
              </figure>
              <ul class="bx-auth-benefits">
                <li><i class="bi bi-clock-history" aria-hidden="true"></i> Historial de turnos con su estado actualizado.</li>
                <li><i class="bi bi-person-check" aria-hidden="true"></i> Tus datos se completan solos al pedir un turno.</li>
                <li><i class="bi bi-envelope-check" aria-hidden="true"></i> Sin publicidad: solo usamos tu contacto para el turno.</li>
              </ul>
            </aside>

            <div class="bx-auth-card">
              <span class="bx-logo-mark bx-auth-mark" aria-hidden="true">BX</span>
              <h1>Crear cuenta</h1>
              <p class="bx-lead">Registrate para ver el historial y el estado de tus turnos.</p>

              <?php if ($errores): ?>
                <div class="alert alert-danger" role="alert">
                  <strong>Revisá el formulario:</strong> <?= e($errores['csrf'] ?? 'encontramos ' . count($errores) . ' error(es) de validación.') ?>
                </div>
              <?php endif; ?>

              <form class="bx-form" method="post" action="registro.php" novalidate>
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>" />
                <p class="bx-required-note">Los campos marcados con <span>*</span> son obligatorios.</p>
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
                    <input class="form-control<?= claseError($errores, 'email') ?>" id="email" name="email" type="email" maxlength="150" required autocomplete="email" placeholder="nombre@correo.com" value="<?= valor($datos, 'email') ?>" />
                    <?= errorCampo($errores, 'email') ?>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="telefono">Teléfono / WhatsApp</label>
                    <input class="form-control<?= claseError($errores, 'telefono') ?>" id="telefono" name="telefono" type="tel" maxlength="20" required autocomplete="tel" placeholder="+54 387 ..." value="<?= valor($datos, 'telefono') ?>" />
                    <?= errorCampo($errores, 'telefono') ?>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="bx-password">
                      <input class="form-control<?= claseError($errores, 'password') ?>" id="password" name="password" type="password" minlength="8" maxlength="72" required autocomplete="new-password" aria-describedby="ayudaPassword requisitosPassword" />
                      <button class="bx-password-toggle" type="button" data-toggle-password aria-controls="password" aria-pressed="false" aria-label="Mostrar contraseña">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                      </button>
                    </div>
                    <?= errorCampo($errores, 'password') ?>
                    <div class="form-text" id="ayudaPassword">Mínimo 8 caracteres, con letras y números.</div>
                    <ul class="bx-requisitos" id="requisitosPassword" data-requisitos="password" data-repetir="password2" aria-label="Requisitos de la contraseña">
                      <li data-regla="largo">Entre 8 y 72 caracteres<span class="visually-hidden"></span></li>
                      <li data-regla="letra">Al menos una letra<span class="visually-hidden"></span></li>
                      <li data-regla="numero">Al menos un número<span class="visually-hidden"></span></li>
                      <li data-regla="coincide">Las dos contraseñas coinciden<span class="visually-hidden"></span></li>
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="password2">Repetir contraseña</label>
                    <div class="bx-password">
                      <input class="form-control<?= claseError($errores, 'password2') ?>" id="password2" name="password2" type="password" maxlength="72" required autocomplete="new-password" />
                      <button class="bx-password-toggle" type="button" data-toggle-password aria-controls="password2" aria-pressed="false" aria-label="Mostrar contraseña">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                      </button>
                    </div>
                    <?= errorCampo($errores, 'password2') ?>
                  </div>
                </div>
                <button class="btn btn-bx-primary btn-lg w-100 mt-4" type="submit"><i class="bi bi-person-plus" aria-hidden="true"></i> Crear cuenta</button>
              </form>
              <p class="bx-auth-alt">¿Ya tenés cuenta? <a href="login.php">Ingresá</a></p>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
