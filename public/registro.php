<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Vitalis | Crear cuenta</title>
  <link rel="stylesheet" href="assets/css/registro.css" />
</head>

<body>
  <main class="auth">
    <section class="auth__left" aria-label="Formulario de registro">
      <div class="panel">
        <header class="brand">
          <img class="brand__logo" src="assets/img/logovitalis.png" alt="Vitalis" />
        </header>

        <h1>Crear cuenta</h1>
        <p class="subtitle">
          Regístrate y comienza tu camino<br />
          hacia una vida más saludable
        </p>

        <form class="form" action="../controllers/RegistroController.php" method="post">
          <label class="field">
            <span class="sr-only">Nombre completo</span>
            <div class="input">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M12 12a4.5 4.5 0 1 0-4.5-4.5A4.5 4.5 0 0 0 12 12Zm0 2c-4.4 0-8 2.1-8 4.7V21h16v-2.3C20 16.1 16.4 14 12 14Z"/></svg>
              </span>
              <input type="text" name="fullName" placeholder="Nombre completo" required />
            </div>
          </label>

          <label class="field">
            <span class="sr-only">Correo electrónico</span>
            <div class="input">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5Z"/></svg>
              </span>
              <input type="email" name="email" placeholder="Correo electrónico" required />
            </div>
          </label>

          <label class="field">
            <span class="sr-only">Contraseña</span>
            <div class="input">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2Zm-7-2a2 2 0 0 1 4 0v2h-4Z"/></svg>
              </span>
              <input type="password" id="password" name="password" placeholder="Contraseña" required />
              <button class="input__action" type="button" onclick="togglePassword('password')" aria-label="Mostrar contraseña">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"/></svg>
              </button>
            </div>
          </label>

          <label class="field">
            <span class="sr-only">Confirmar contraseña</span>
            <div class="input">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2Zm-7-2a2 2 0 0 1 4 0v2h-4Z"/></svg>
              </span>
              <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmar contraseña" required />
              <button class="input__action" type="button" onclick="togglePassword('confirmPassword')" aria-label="Mostrar contraseña">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"/></svg>
              </button>
            </div>
          </label>

          <button class="btn" type="submit">Registrarse</button>

          <p class="footer">
            ¿Ya tienes cuenta? <a class="link" href="login.php">Inicia Sesión</a>
          </p>
        </form>
      </div>
    </section>

    <section class="auth__right" aria-hidden="true"></section>
  </main>

  <script>
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      if (input.type === "password") {
        input.type = "text";
      } else {
        input.type = "password";
      }
    }
  </script>
</body>
</html>