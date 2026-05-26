<?php
/**
 * Filtro de palabras prohibidas — Vitalis
 * Español e Inglés
 */

define('PALABRAS_PROHIBIDAS', [
    // ── ESPAÑOL ──
    'puta','putas','puto','putos','puta madre','putamadre',
    'cabron','cabrón','cabrones','cabrona','cabronas',
    'pendejo','pendejos','pendeja','pendejas',
    'chinga','chingada','chingadas','chingado','chingados','chingon','chingona',
    'pinche','pinches',
    'culero','culeros','culera','culeras','culo','culos',
    'mierda','mierdas',
    'mamada','mamadas','mamar',
    'verga','vergas',
    'güey','wey','weyes','buey',
    'pedo','pedos',
    'coger','cogiendo',
    'joder','jodido','jodida',
    'hostia','hostias',
    'gilipollas',
    'idiota','idiotas',
    'imbecil','imbécil','imbeciles','imbéciles',
    'estupido','estúpido','estupida','estúpida',
    'marica','maricas','maricón','maricon',
    'perra','perras',
    'zorra','zorras',
    'prostituta','prostitutas',
    'carajo','carajos',
    'coño','coños',
    'hijoputa','hijo de puta','hijueputa','hijo de tu puta madre',
    'hijo de tu reputisima madre','reputisima','reputa','repútisima',
    'desgraciado','desgraciada','desgraciados',
    'bastardo','bastarda','bastardos',
    'maldito','maldita','malditos','malditas',
    'subnormal','subnormales',
    'retrasado','retrasada','retrasados',
    'nazi','nazis',
    // Genitales y actos sexuales
    'pene','penes','pito','pitos','pija','picha',
    'teta','tetas','chichi','chichis',
    'vagina','vaginas','vulva',
    'ano','culo',
    'sexo','sexual',
    'masturbacion','masturbación','masturbar','masturbarse',
    'orgasmo','orgasmos',
    'eyacular','eyaculacion',
    'porno','pornografia','pornografía',
    'cara de pene','cara de culo','cara de pito',
    'comeme','cómeme','chupame','chúpame',
    'lameme','lámeme','meteme','méteme',
    'chupa','chupar',
    'folla','follar','follando',
    'pene chico','pito chico','pito chuo',
    'señor cara','señora cara',
    // Variaciones creativas de Amador
    'perrita','perritas','perrito','perritos',
    'no le sabe la perrita','no le sabes perrita',
    'perrita gay','perritos gay',
    'triple x','triplex','xxx','xxxx',
    'bikini','bikinis',
    'sin ropa','desnuda','desnudo','desnudas','desnudos',
    'encuera','encueras','encuerada','encueradas',
    'pack','packs','nudes','nude',
    'onlyfans','only fans',
    'stripper','strippers',
    'escort','escorts',
    'putita','putitas','putito','putitos',
    'culito','culitos','culona','culonas',
    'nalgona','nalgonas','nalgas','nalga',
    'tetona','tetonas',
    'buenota','buenotas','buenote','buenotes',
    'caliente','cachonda','cachondo','cachondas','cachondos',
    'excitada','excitado','excitadas','excitados',
    'sexting','sextear',
    'porno gratis','ver porno',
    'lesbiana','lesbianas',
    'gay porn','porn hub','pornhub','xvideos','xnxx','redtube',

    // ── INGLÉS ──
    'fuck','fucking','fucked','fucker','fuckers','fucks',
    'shit','shits','shitting','shitty',
    'bitch','bitches','bitching',
    'asshole','assholes',
    'ass','asses',
    'bastard','bastards',
    'cunt','cunts',
    'dick','dicks','dickhead','dickheads',
    'cock','cocks',
    'pussy','pussies',
    'whore','whores',
    'slut','sluts',
    'damn','damned',
    'crap','crappy',
    'piss','pissed',
    'nigger','niggers','nigga','niggas',
    'faggot','faggots','fag','fags',
    'retard','retards','retarded',
    'idiot','idiots',
    'moron','morons',
    'stupid','stupids',
    'dumbass','dumbasses',
    'motherfucker','motherfuckers','motherfucking',
    'son of a bitch','sonofabitch',
    'bullshit',
    'jackass','jackasses',
    'dipshit','dipshits',
    'douche','douchebag','douchebags',
    'scumbag','scumbags',
    'shithead','shitheads',
    'asshat','asshats',
    'hell','go to hell',
]);

/**
 * Verifica si el texto contiene palabras prohibidas.
 * Devuelve true si el texto ES LIMPIO, false si contiene groserías.
 */
function textoEsLimpio($texto) {
    // Versión normal con espacios
    $textoLower = mb_strtolower($texto, 'UTF-8');
    $textoNorm  = strtr($textoLower, [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
        'à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u',
        'ü'=>'u','ñ'=>'n',
    ]);

    // Versión sin espacios (para detectar "pu ta", "f u c k", etc.)
    $textoSinEspacios = str_replace([' ', '.', '_', '-', '*', '0'], '', $textoNorm);

    foreach (PALABRAS_PROHIBIDAS as $palabra) {
        $palabraNorm = strtr(mb_strtolower($palabra, 'UTF-8'), [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
            'à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u',
            'ü'=>'u','ñ'=>'n',
        ]);
        $palabraSinEspacios = str_replace(' ', '', $palabraNorm);

        // Verificar en texto normal
        if (mb_strpos($textoNorm, $palabraNorm) !== false) return false;

        // Verificar en texto sin espacios (detecta "pu ta", "f.u.c.k", etc.)
        if (mb_strpos($textoSinEspacios, $palabraSinEspacios) !== false) return false;
    }
    return true;
}
?>