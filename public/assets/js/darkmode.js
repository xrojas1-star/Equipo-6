/**
 * Dark Mode + Google Translate Global — Vitalis
 */
(function() {
    const KEY = 'vitalis_darkmode';

    // Migrar key antigua
    const keyVieja = localStorage.getItem('darkMode');
    if (keyVieja !== null) {
        localStorage.setItem(KEY, keyVieja);
        localStorage.removeItem('darkMode');
    }

    // Aplicar dark mode INMEDIATAMENTE
    if (localStorage.getItem(KEY) === 'true') {
        document.documentElement.classList.add('dark-mode');
        if (document.body) document.body.classList.add('dark-mode');
    }

    // Estilos botones flotantes
    const styleGT = document.createElement('style');
    styleGT.textContent = `
        .goog-te-banner-frame, .skiptranslate { display: none !important; }
        body { top: 0 !important; }
        #google_translate_element { display: none !important; }
        #vitalis-float-btns {
            position: fixed !important;
            bottom: 24px !important;
            left: 24px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
            z-index: 99999 !important;
        }
        #vitalis-float-btns button {
            width: 48px !important;
            height: 48px !important;
            border-radius: 50% !important;
            border: none !important;
            cursor: pointer !important;
            font-size: 20px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.25) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: transform 0.2s, box-shadow 0.2s !important;
            background: #ffffff !important;
            color: #1e293b !important;
        }
        #vitalis-float-btns button:hover {
            transform: scale(1.1) !important;
            box-shadow: 0 6px 20px rgba(0,0,0,0.35) !important;
        }
        #btn-idioma-float {
            font-size: 12px !important;
            font-weight: 700 !important;
            letter-spacing: 0.3px !important;
        }
        body.dark-mode #vitalis-float-btns button {
            background: #1e293b !important;
            color: #f1f5f9 !important;
        }
    `;
    document.head.appendChild(styleGT);

    function aplicarDarkModeAlBody() {
        if (localStorage.getItem(KEY) === 'true') {
            document.body.classList.add('dark-mode');
        }
    }

    // Cargar Google Translate
    window.googleTranslateElementInit = function() {
        new google.translate.TranslateElement(
            { pageLanguage: 'es', includedLanguages: 'en', autoDisplay: false },
            'google_translate_element'
        );
        if (localStorage.getItem('vitalis_idioma') === 'en') {
            setTimeout(() => _aplicarEN(), 400);
        }
    };

    const gtScript = document.createElement('script');
    gtScript.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    gtScript.async = true;
    document.head.appendChild(gtScript);

    function crearBotones() {
        aplicarDarkModeAlBody();

        if (document.getElementById('vitalis-float-btns')) return;

        if (!document.getElementById('google_translate_element')) {
            const gtDiv = document.createElement('div');
            gtDiv.id = 'google_translate_element';
            document.body.appendChild(gtDiv);
        }

        const container = document.createElement('div');
        container.id = 'vitalis-float-btns';

        const btnDark = document.createElement('button');
        btnDark.id = 'btn-darkmode-float';
        btnDark.title = 'Alternar modo oscuro';
        btnDark.innerHTML = localStorage.getItem(KEY) === 'true' ? '☀️' : '🌙';
        btnDark.addEventListener('click', toggleDarkMode);

        const btnIdioma = document.createElement('button');
        btnIdioma.id = 'btn-idioma-float';
        btnIdioma.title = 'Cambiar idioma';
        const idiomaActual = localStorage.getItem('vitalis_idioma') || 'es';
        btnIdioma.innerHTML = idiomaActual === 'es' ? '🌐 EN' : '🌐 ES';
        btnIdioma.addEventListener('click', toggleIdioma);

        container.appendChild(btnDark);
        container.appendChild(btnIdioma);
        document.body.appendChild(container);

        const checkbox = document.getElementById('modo-oscuro-check');
        if (checkbox) {
            checkbox.checked = localStorage.getItem(KEY) === 'true';
            checkbox.addEventListener('change', () => {
                const isDark = checkbox.checked;
                document.body.classList.toggle('dark-mode', isDark);
                localStorage.setItem(KEY, isDark);
                btnDark.innerHTML = isDark ? '☀️' : '🌙';
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', crearBotones);
    } else {
        crearBotones();
    }

    function _aplicarEN() {
        const select = document.querySelector('.goog-te-combo');
        if (select) {
            select.value = 'en';
            select.dispatchEvent(new Event('change'));
        }
    }

    window.toggleIdioma = function() {
        const btn = document.getElementById('btn-idioma-float');
        const idioma = localStorage.getItem('vitalis_idioma') || 'es';

        if (idioma === 'es') {
            btn.innerHTML = '⏳';
            _aplicarEN();
            localStorage.setItem('vitalis_idioma', 'en');
            setTimeout(() => { btn.innerHTML = '🌐 ES'; }, 800);
        } else {
            btn.innerHTML = '⏳';
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + location.hostname;
            localStorage.setItem('vitalis_idioma', 'es');
            location.reload();
        }
    };

    window.toggleDarkMode = function() {
        const isDark = document.body.classList.toggle('dark-mode');
        localStorage.setItem(KEY, isDark);
        const btn = document.getElementById('btn-darkmode-float');
        if (btn) btn.innerHTML = isDark ? '☀️' : '🌙';
        const checkbox = document.getElementById('modo-oscuro-check');
        if (checkbox) checkbox.checked = isDark;
    };

})();