<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vitalis | Recuperar contraseña</title>
  <link rel="stylesheet" href="assets/css/login.css">
  <style>
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--brand);
      font-size: 14px;
      text-decoration: none;
      margin-bottom: 20px;
      font-weight: 600;
      transition: opacity 0.2s;
    }
    .back-link:hover { opacity: 0.75; }
    .info-box {
      background: rgba(10,166,197,0.08);
      border: 1px solid rgba(10,166,197,0.25);
      border-radius: 14px;
      padding: 12px 16px;
      font-size: 13.5px;
      color: #0077a8;
      margin-bottom: 18px;
      line-height: 1.5;
    }
    .token-box {
      display: none;
      background: #f0fdf4;
      border: 1px solid #86efac;
      border-radius: 14px;
      padding: 16px;
      margin-top: 16px;
      text-align: center;
    }
    .token-box p { margin: 0 0 10px; font-size: 13.5px; color: #15803d; }
    .token-box a {
      display: inline-block;
      background: #22c55e;
      color: #fff;
      padding: 10px 22px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 14px;
      text-decoration: none;
      transition: background 0.2s;
    }
    .token-box a:hover { background: #16a34a; }
    .field-error { font-size: 12px; color: #ef4444; padding-left: 14px; min-height: 16px; }
  </style>
</head>
<body>
  <main class="login">
    <section class="login__left">
      <div class="panel">
        <header class="brand">
          <img class="brand__logo" src="assets/img/logovitalis.png" alt="Vitalis">
        </header>

        <a href="login.php" class="back-link">← Volver al inicio de sesión</a>

        <h1 style="font-size: clamp(24px,3.5vw,34px); margin-bottom: 8px;">Recuperar<br>contraseña</h1>

        <div class="info-box">
          Ingresa tu correo registrado y te daremos un enlace para restablecer tu contraseña.
        </div>

        <form class="form" id="recForm" novalidate>
          <label class="field">
            <input type="email" id="correo" placeholder="Correo electrónico" required autocomplete="email">
          </label>
          <span class="field-error" id="err-correo"></span>
          <button class="btn" type="submit" id="btn-enviar">Obtener enlace</button>
        </form>

        <!-- Resultado: enlace generado -->
        <div class="token-box" id="token-box">
          <p>✅ Correo verificado. Haz clic para restablecer tu contraseña:</p>
          <a id="reset-link" href="#">Restablecer contraseña</a>
        </div>

      </div>
    </section>
    <section class="login__right" aria-hidden="true"></section>
  </main>

  <script>
    document.getElementById('recForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const correo = document.getElementById('correo').value.trim();
      const errEl  = document.getElementById('err-correo');
      const btn    = document.getElementById('btn-enviar');

      // Validar formato
      if (!/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(correo)) {
        errEl.textContent = 'Ingresa un correo válido.';
        return;
      }
      errEl.textContent = '';
      btn.disabled = true;
      btn.textContent = 'Verificando...';

      try {
        const fd = new FormData();
        fd.append('correo', correo);
        const res  = await fetch('/controllers/RecuperarController.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
          document.getElementById('token-box').style.display = 'block';
          document.getElementById('reset-link').href = '/restablecer_password.php?token=' + data.token;
          document.getElementById('recForm').style.display = 'none';
        } else {
          errEl.textContent = data.message || 'No se encontró ninguna cuenta con ese correo.';
          btn.disabled = false;
          btn.textContent = 'Obtener enlace';
        }
      } catch(err) {
        errEl.textContent = 'Error de conexión. Intenta de nuevo.';
        btn.disabled = false;
        btn.textContent = 'Obtener enlace';
      }
    });
  </script>
</body>
</html>
