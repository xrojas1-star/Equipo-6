<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vitalis | Nueva contraseña</title>
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="stylesheet" href="assets/css/registro.css?v=2">
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
    .error-page {
      text-align: center;
      padding: 20px 0;
    }
    .error-page p { color: #ef4444; font-size: 15px; margin-bottom: 16px; }
  </style>
</head>
<body>
  <main class="login">
    <section class="login__left">
      <div class="panel">
        <header class="brand">
          <img class="brand__logo" src="assets/img/logovitalis.png" alt="Vitalis">
        </header>

        <?php
        $token = trim($_GET['token'] ?? '');
        if (empty($token) || strlen($token) !== 64):
        ?>
        <div class="error-page">
          <p>⚠️ Enlace inválido o expirado.</p>
          <a href="recuperar_password.php" style="color:var(--brand); font-weight:bold;">Solicitar nuevo enlace</a>
        </div>
        <?php else: ?>

        <a href="login.php" class="back-link">← Volver al inicio de sesión</a>
        <h1 style="font-size: clamp(24px,3.5vw,34px); margin-bottom: 18px;">Nueva<br>contraseña</h1>

        <form class="form" id="resetForm" novalidate>
          <input type="hidden" id="token" value="<?php echo htmlspecialchars($token); ?>">

          <!-- Nueva contraseña -->
          <label class="field">
            <span class="sr-only">Nueva contraseña</span>
            <div class="input" id="wrap-password">
              <span class="input__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2Zm-7-2a2 2 0 0 1 4 0v2h-4Z"/></svg>
              </span>
              <input type="password" id="password" placeholder="Nueva contraseña" required autocomplete="new-password">
              <button class="input__action" type="button" onclick="togglePw('password')" aria-label="Mostrar">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"/></svg>
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
              <input type="password" id="confirm" placeholder="Confirmar contraseña" required autocomplete="new-password">
              <button class="input__action" type="button" onclick="togglePw('confirm')" aria-label="Mostrar">
                <svg viewBox="0 0 24 24" width="22" height="22"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"/></svg>
              </button>
            </div>
            <span class="field-error" id="err-confirm"></span>
          </label>

          <!-- Barra fortaleza + requisitos -->
          <div id="strength-bar" class="strength-bar">
            <span id="bar1"></span><span id="bar2"></span>
            <span id="bar3"></span><span id="bar4"></span>
          </div>
          <p id="strength-text" class="strength-text"></p>
          <ul id="req-list" class="req-list">
            <li id="req-len">Mínimo 8 caracteres</li>
            <li id="req-upper">Al menos 1 letra mayúscula</li>
            <li id="req-num">Al menos 1 número</li>
            <li id="req-special">Al menos 1 carácter especial (@$!%*?&amp;#.)</li>
          </ul>

          <button class="btn" type="submit" id="btn-guardar">Guardar contraseña</button>
        </form>

        <?php endif; ?>
      </div>
    </section>
    <section class="login__right" aria-hidden="true"></section>
  </main>

  <script>
    function togglePw(id) {
      const i = document.getElementById(id);
      i.type = i.type === 'password' ? 'text' : 'password';
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

    const pwInput = document.getElementById('password');
    if (pwInput) {
      pwInput.addEventListener('input', function() {
        const pw    = this.value;
        const res   = validarPassword(pw);
        const score = Object.values(res).filter(Boolean).length;
        const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
        const labels = ['Muy débil','Débil','Aceptable','Segura'];

        document.getElementById('strength-bar').classList.add('visible');
        document.getElementById('strength-text').classList.add('visible');
        document.getElementById('req-list').classList.add('visible');

        ['bar1','bar2','bar3','bar4'].forEach((id,i) => {
          document.getElementById(id).style.background = i < score ? colors[score-1] : '#e2e8f0';
        });

        const txt = document.getElementById('strength-text');
        txt.textContent = pw.length ? (labels[score-1] || '') : '';
        txt.style.color = score > 0 ? colors[score-1] : '#94a3b8';

        document.getElementById('req-len').classList.toggle('ok',     res.len);
        document.getElementById('req-upper').classList.toggle('ok',   res.upper);
        document.getElementById('req-num').classList.toggle('ok',     res.num);
        document.getElementById('req-special').classList.toggle('ok', res.special);

        const conf = document.getElementById('confirm').value;
        if (conf.length > 0) setField('wrap-confirm', pw === conf, 'err-confirm', 'Las contraseñas no coinciden.');
      });

      document.getElementById('confirm').addEventListener('input', function() {
        setField('wrap-confirm', this.value === pwInput.value, 'err-confirm', 'Las contraseñas no coinciden.');
      });

      document.getElementById('resetForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const pw   = pwInput.value;
        const conf = document.getElementById('confirm').value;
        const res  = validarPassword(pw);
        let ok     = true;

        if (!res.len || !res.upper || !res.num || !res.special) {
          setField('wrap-password', false, 'err-password', 'La contraseña no cumple los requisitos.');
          document.getElementById('strength-bar').classList.add('visible');
          document.getElementById('strength-text').classList.add('visible');
          document.getElementById('req-list').classList.add('visible');
          ok = false;
        }
        if (pw !== conf) {
          setField('wrap-confirm', false, 'err-confirm', 'Las contraseñas no coinciden.');
          ok = false;
        }
        if (!ok) return;

        const btn = document.getElementById('btn-guardar');
        btn.disabled = true;
        btn.textContent = 'Guardando...';

        try {
          const fd = new FormData();
          fd.append('token', document.getElementById('token').value);
          fd.append('password', pw);
          const response = await fetch('/controllers/RestablecerController.php', { method: 'POST', body: fd });
          const data     = await response.json();

          if (data.success) {
            alert('✅ Contraseña actualizada correctamente. Inicia sesión.');
            window.location.href = '/login.php';
          } else {
            alert('❌ ' + (data.message || 'Error al actualizar. El enlace puede haber expirado.'));
            btn.disabled = false;
            btn.textContent = 'Guardar contraseña';
          }
        } catch(err) {
          alert('Error de conexión. Intenta de nuevo.');
          btn.disabled = false;
          btn.textContent = 'Guardar contraseña';
        }
      });
    }
  </script>
</body>
</html>
