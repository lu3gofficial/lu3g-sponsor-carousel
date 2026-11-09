# LU3G Sponsor Carousel

Widget Elementor per i loghi degli sponsor, con più stili: marquee, doppia riga, slider (Swiper di Elementor),
logo wall e griglia. Funziona ovunque, compreso il footer del Theme Builder.

Plugin WordPress di LU3G Agenzia Web. Requisiti: PHP 7.4+, WordPress 6.0+, Elementor 3.5+ (widget classico,
`\Elementor\Widget_Base`).

## Stato dello sviluppo
Il plugin si costruisce a step, ognuno pubblicato come release da provare:

1. ✅ Bootstrap, controllo di Elementor, categoria "LU3G", widget vuoto (0.1.0)
2. ✅ Repeater dei loghi e stile griglia (0.2.0)
3. ✅ Marquee e doppia riga, con sfumatura ai bordi (0.3.0)
4. ✅ Slider con lo Swiper di Elementor (0.4.0)
5. ✅ Logo wall (0.5.0)
6. ✅ Tab Stile completo, effetti hover, tooltip (0.6.0)
7. ✅ Rifinitura: movimento ridotto, accessibilità, pulsante pausa, traduzioni (1.0.0)

## Installazione su un sito
1. Scarica `lu3g-sponsor-carousel.zip` dall'ultima [release](../../releases/latest).
2. WordPress → Plugin → Aggiungi nuovo → Carica plugin → Attiva.
3. Gli aggiornamenti arrivano da soli dalle release di questo repository (Bacheca → Aggiornamenti, entro 6 ore;
   subito con "Controlla di nuovo" o con il link "Controlla aggiornamenti" nella pagina Plugin).
   Facoltativo: `define( 'LU3G_GITHUB_TOKEN', 'github_pat_...' );` in `wp-config.php` alza il limite di richieste all'API GitHub.

## Struttura
- `lu3g-sponsor-carousel.php` – intestazione, controllo di Elementor con avviso in bacheca, avvio, aggiornamenti.
- `includes/class-plugin.php` – categoria "LU3G", registrazione del widget e degli asset.
- `includes/widgets/class-sponsor-carousel-widget.php` – il widget.
- `includes/class-updater.php` – aggiornamenti dalle release GitHub. **Generato** da [lu3g-updater](https://github.com/lu3gofficial/lu3g-updater) con i dati di `.lu3g-updater`: non modificarlo a mano, rigeneralo con `sh ../lu3g-updater/bin/sync.sh .`.
- `assets/css/sponsor-carousel.css`, `assets/js/sponsor-carousel.js` – stile e handler frontend di Elementor.
- `languages/` – traduzioni.

Prefissi: `lu3g-sc` per classi CSS e handle, `LU3G_SC_` per classi PHP e costanti, `lu3g_sc_` per le funzioni.

## Pubblicare una nuova versione
1. Aumenta la versione in `lu3g-sponsor-carousel.php` (`Version:` e `LU3G_SC_VERSION`).
2. Aggiungi la sezione della versione in `CHANGELOG.md` (diventa la nota della release).
3. Commit, poi `git tag vX.Y.Z && git push && git push --tags`: l'automazione crea lo zip e pubblica la release.
   Senza computer: dopo il merge su `main`, su GitHub *Actions → Release → Run workflow* (branch `main`). Il tag lo crea
   l'automazione dalla `Version` del plugin; se quella versione è già pubblicata si ferma.

Pacchetto in locale: `sh bin/build.sh` → `dist/lu3g-sponsor-carousel.zip`.
