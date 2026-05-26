<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Vitalis | Crear cuenta</title>
  <link rel="stylesheet" href="assets/css/registro.css?v=2" />
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

        <form class="form" id="registerForm" action="/controllers/RegistroController.php" method="post" novalidate>

          <!-- Nombre -->
          <label class="field">
            <span class="sr-only">Nombre completo</span>
            <div class="input" id="wrap-nombre">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M12 12a4.5 4.5 0 1 0-4.5-4.5A4.5 4.5 0 0 0 12 12Zm0 2c-4.4 0-8 2.1-8 4.7V21h16v-2.3C20 16.1 16.4 14 12 14Z"/></svg>
              </span>
              <input type="text" name="fullName" id="fullName" placeholder="Nombre completo" required autocomplete="name" />
            </div>
            <span class="field-error" id="err-nombre"></span>
          </label>

          <!-- Correo -->
          <label class="field">
            <span class="sr-only">Correo electrónico</span>
            <div class="input" id="wrap-email">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5Z"/></svg>
              </span>
              <input type="email" name="email" id="email" placeholder="Correo electrónico" required autocomplete="email" />
            </div>
            <span class="field-error" id="err-email"></span>
          </label>

          <!-- Contraseña -->
          <label class="field">
            <span class="sr-only">Contraseña</span>
            <div class="input" id="wrap-password">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2Zm-7-2a2 2 0 0 1 4 0v2h-4Z"/></svg>
              </span>
              <input type="password" id="password" name="password" placeholder="Contraseña" required autocomplete="new-password" />
              <button class="input__action" type="button" onclick="togglePassword('password')" aria-label="Mostrar contraseña">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"/></svg>
              </button>
            </div>
            <span class="field-error" id="err-password"></span>
          </label>

          <!-- Confirmar contraseña -->
          <label class="field">
            <span class="sr-only">Confirmar contraseña</span>
            <div class="input" id="wrap-confirm">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2Zm-7-2a2 2 0 0 1 4 0v2h-4Z"/></svg>
              </span>
              <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmar contraseña" required autocomplete="new-password" />
              <button class="input__action" type="button" onclick="togglePassword('confirmPassword')" aria-label="Mostrar contraseña">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"/></svg>
              </button>
            </div>
            <span class="field-error" id="err-confirm"></span>
          </label>

          <!-- Barra + texto + requisitos (ocultos, aparecen al escribir contraseña) -->
          <div id="strength-bar" class="strength-bar">
            <span id="bar1"></span>
            <span id="bar2"></span>
            <span id="bar3"></span>
            <span id="bar4"></span>
          </div>
          <p id="strength-text" class="strength-text"></p>
          <ul id="req-list" class="req-list">
            <li id="req-len">Mínimo 8 caracteres</li>
            <li id="req-upper">Al menos 1 letra mayúscula</li>
            <li id="req-num">Al menos 1 número</li>
            <li id="req-special">Al menos 1 carácter especial (@$!%*?&amp;#.)</li>
          </ul>

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
    function togglePassword(id) {
      const i = document.getElementById(id);
      i.type = i.type === 'password' ? 'text' : 'password';
    }

    function validarEmail(e) {
      const dominiosPermitidos = [
        'gmail.com','hotmail.com','outlook.com',
        'yahoo.com','ucol.mx','live.com','icloud.com'
      ];
      const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
      if (!regex.test(e.trim())) return false;
      const dominio = e.trim().split('@')[1].toLowerCase();
      return dominiosPermitidos.includes(dominio);
    }

    function validarPassword(pw) {
      return {
        len:     pw.length >= 8,
        upper:   /[A-Z]/.test(pw),
        num:     /[0-9]/.test(pw),
        special: /[@$!%*?&#.]/.test(pw)
      };
    }

    function setField(wrapId, ok, errId, msg) {
      const w = document.getElementById(wrapId);
      const e = document.getElementById(errId);
      w.classList.remove('input--ok','input--error');
      w.classList.add(ok ? 'input--ok' : 'input--error');
      e.textContent = ok ? '' : msg;
    }

    document.getElementById('fullName').addEventListener('blur', function() {
      setField('wrap-nombre', this.value.trim().length >= 2, 'err-nombre', 'Ingresa tu nombre completo.');
    });

    document.getElementById('email').addEventListener('blur', function() {
      setField('wrap-email', validarEmail(this.value), 'err-email', 'Solo se aceptan: gmail.com, hotmail.com, outlook.com, yahoo.com, ucol.mx, live.com, icloud.com');
    });

    document.getElementById('password').addEventListener('input', function() {
      const pw    = this.value;
      const res   = validarPassword(pw);
      const score = Object.values(res).filter(Boolean).length;
      const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
      const labels = ['Muy débil','Débil','Aceptable','Segura'];

      document.getElementById('strength-bar').classList.add('visible');
      document.getElementById('strength-text').classList.add('visible');
      document.getElementById('req-list').classList.add('visible');

      ['bar1','bar2','bar3','bar4'].forEach((id, i) => {
        document.getElementById(id).style.background = i < score ? colors[score-1] : '#e2e8f0';
      });

      const txt = document.getElementById('strength-text');
      txt.textContent = pw.length ? (labels[score-1] || '') : '';
      txt.style.color = score > 0 ? colors[score-1] : '#94a3b8';

      document.getElementById('req-len').classList.toggle('ok',     res.len);
      document.getElementById('req-upper').classList.toggle('ok',   res.upper);
      document.getElementById('req-num').classList.toggle('ok',     res.num);
      document.getElementById('req-special').classList.toggle('ok', res.special);

      const conf = document.getElementById('confirmPassword').value;
      if (conf.length > 0) setField('wrap-confirm', pw === conf, 'err-confirm', 'Las contraseñas no coinciden.');
    });

    document.getElementById('confirmPassword').addEventListener('input', function() {
      setField('wrap-confirm', this.value === document.getElementById('password').value, 'err-confirm', 'Las contraseñas no coinciden.');
    });

    document.getElementById('registerForm').addEventListener('submit', function(e) {
      let ok = true;
      if (document.getElementById('fullName').value.trim().length < 2) {
        setField('wrap-nombre', false, 'err-nombre', 'Ingresa tu nombre completo.'); ok = false;
      }
      if (!validarEmail(document.getElementById('email').value)) {
        setField('wrap-email', false, 'err-email', 'Solo se aceptan: gmail.com, hotmail.com, outlook.com, yahoo.com, ucol.mx, live.com, icloud.com'); ok = false;
      }
      const pw  = document.getElementById('password').value;
      const res = validarPassword(pw);
      if (!res.len || !res.upper || !res.num || !res.special) {
        setField('wrap-password', false, 'err-password', 'La contraseña no cumple los requisitos.');
        document.getElementById('strength-bar').classList.add('visible');
        document.getElementById('strength-text').classList.add('visible');
        document.getElementById('req-list').classList.add('visible');
        ok = false;
      }
      if (pw !== document.getElementById('confirmPassword').value) {
        setField('wrap-confirm', false, 'err-confirm', 'Las contraseñas no coinciden.'); ok = false;
      }
      if (!ok) e.preventDefault();
    });
  </script>
</body>
</html>