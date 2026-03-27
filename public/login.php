<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vitalis | Iniciar sesión</title>
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
  <main class="login">
    <section class="login__left">
      <div class="panel">
        <header class="brand">
          <img class="brand__logo" src="assets/img/logovitalis.png" alt="Vitalis">
        </header>

        <h1>Bienvenido<br>de nuevo</h1>

        <form class="form" action="../controllers/LoginController.php" method="post">
          <label class="field">
            <input type="email" name="email" placeholder="Correo electrónico" required>
          </label>

          <label class="field password-field">
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <button class="input__action" type="button" onclick="togglePassword('password')" aria-label="Mostrar contraseña">
              <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"/></svg>
            </button>
          </label>

          <button class="btn" type="submit">Iniciar Sesión</button>

          <p class="forgot">
            <a href="#">¿Olvidaste tu contraseña?</a><br><br>
            <a href="registro.php" style="color: var(--brand); font-weight: bold;">¿No tienes cuenta? Regístrate aquí</a>
          </p>
        </form>
      </div>
    </section>

    <section class="login__right" aria-hidden="true"></section>
  </main>

  <script>
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      input.type = input.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>