# Registro delle modifiche

## 1.1.1
- `includes/class-updater.php` ora è generato dal modello comune di [lu3g-updater](https://github.com/lu3gofficial/lu3g-updater) (1.0.0), a partire dal nuovo file `.lu3g-updater`. Il codice dell'updater è lo stesso di prima: cambia solo il commento in cima. Nessun cambiamento nel widget.

## 1.1.0
- *Layout → Blocca lo scorrimento*, per desktop, tablet e mobile (vuoto = come il dispositivo più grande). Con "Sì, loghi fermi": marquee e doppia riga diventano righe di loghi centrate che vanno a capo, senza copie né sfumatura; il logo wall non sostituisce più i loghi; lo slider non scorre da solo (frecce e pallini restano); il pulsante pausa sparisce. Funziona con il solo CSS (media query di Elementor), quindi vale da subito e si vede nell'editor cambiando dispositivo.

## 1.0.0
- Step 7, rifinitura: prima versione completa.
- *Pulsante pausa* facoltativo (Layout) per marquee, doppia riga, logo wall e slider con autoplay: ferma e riprende il movimento, con aria-pressed ed etichetta che cambia. Richiesto dalle regole di accessibilità (WCAG 2.2.2) per i contenuti che si muovono da soli; su telefono non c'è il passaggio del mouse.
- Uno sponsor con link ma senza nome e senza testo alternativo ha comunque un nome per gli screen reader ("Sponsor: dominio").
- Movimento ridotto: niente transizioni negli effetti e nel tooltip, pulsante pausa nascosto (non si muove nulla).
- File delle traduzioni `languages/lu3g-sponsor-carousel.pot` e `readme.txt`.

## 0.6.0
- Step 6: tab Stile completa.
- *Effetto*, normale e hover (vale anche col focus da tastiera): scala di grigi, opacità, scala, durata della transizione. L'hover eredita il normale se non è impostato.
- *Box del logo*: spaziatura interna, arrotondamento, sfondo, bordo e ombra, con sfondo, colore del bordo e ombra in hover.
- *Frecce e pallini* (slider): posizione delle frecce (ai lati fuori, ai lati sopra i loghi, sotto al centro), dimensione, colori e sfondi normale/hover; pallini con dimensione, colore, colore dell'attivo, allineamento e distanza.
- *Tooltip*: il nome dello sponsor sopra il logo al passaggio del mouse e col focus; colori, tipografia, spaziatura e arrotondamento. È un solo elemento fuori dalle righe che scorrono, così marquee e slider non lo tagliano.

## 0.5.2
- Corretto davvero l'errore critico nell'anteprima dell'editor (la 0.5.1 non bastava): sul widget "modello" senza dati anche get_data() va in errore. Ora nell'editor il CSS di Swiper si carica senza leggere le impostazioni, e fuori dall'editor lo stile si legge solo sui widget veri (is_type_instance).

## 0.5.1
- Corretto: con la 0.4.0 e la 0.5.0 l'anteprima dell'editor di Elementor andava in errore critico e l'editor restava in caricamento. Il widget leggeva le impostazioni anche quando Elementor lo prepara senza dati; ora le legge solo se ci sono.

## 0.5.0
- Step 5: stile *Logo wall*. Slot visibili per dispositivo e colonne (vuoto = tutti gli slot su una riga); a ogni intervallo un logo a caso svanisce e al suo posto entra uno che non si vede, mai due uguali a schermo e mai lo stesso slot due volte di fila.
- Intervallo di sostituzione e durata della dissolvenza; pausa al passaggio del mouse e col focus da tastiera; niente sostituzioni a scheda nascosta.
- Un logo in arrivo non ancora caricato viene caricato prima di entrare.
- Senza JavaScript o con movimento ridotto: griglia statica con tutti i loghi.
- Tutti gli stili sono pronti: tolto l'avviso "stile non ancora pronto".

## 0.4.0
- Step 4: stile *Slider* con lo Swiper incluso in Elementor (elementorFrontend.utils.swiper), senza caricarne un altro. Classe del contenitore giusta per Swiper 5 o 8 (funzione "e_swiper_latest" e elementorFrontend.config.swiperClass); CSS di Swiper caricato solo con lo slider.
- Loghi visibili per desktop, tablet e mobile (punti di passaggio di Elementor), autoplay con intervallo, ciclo infinito (spento da solo se i loghi non bastano), frecce, pallini, velocità della transizione. Spazio tra i loghi dalla spaziatura dello stile.
- Autoplay in pausa al passaggio del mouse e col focus da tastiera; con "riduci animazioni" niente autoplay e transizioni immediate.
- Swiper viene chiuso quando Elementor ridisegna il widget nell'editor.

## 0.3.0
- Step 3: stili *Marquee* e *Doppia riga*. Scorrimento continuo solo CSS (lista + copia con aria-hidden, spostamento di -50%), senza stacchi a ogni giro.
- Durata di un giro, direzione (nella doppia riga la seconda riga va al contrario), pausa al passaggio del mouse e col focus da tastiera, sfumatura ai bordi con mask-image e larghezza regolabile per dispositivo.
- Con pochi loghi la riga viene riempita ripetendoli (ripetizioni nascoste agli screen reader e fuori dal Tab) e la durata cresce nella stessa misura: la velocità resta quella scelta.
- Doppia riga: loghi alternati tra le due righe; con un solo logo, una riga.
- Nel marquee i loghi si caricano subito (non lazy), per non vederli comparire mentre scorrono.
- Movimento ridotto: niente scorrimento, i loghi vanno a capo come una griglia.

## 0.2.0
- Step 2: repeater dei loghi (logo, logo alternativo, nome, link con rel="sponsored noopener" e nuova scheda dalle opzioni del link) e stile *Griglia* con colonne per dispositivo.
- *Usa il logo alternativo*, dimensione dell'immagine, nome della lista per gli screen reader.
- Stile → Loghi: altezza massima (px/vh), larghezza massima, spaziatura, allineamento verticale, tramite variabili CSS.
- Immagini con wp_get_attachment_image (srcset, width e height); misure lette dagli SVG; spazio riservato anche prima del caricamento.
- Gli stili non ancora pronti (marquee, doppia riga, slider, logo wall) si vedono come griglia, con un avviso nel pannello.

## 0.1.0
- Step 1: bootstrap del plugin, controllo di Elementor (avviso in bacheca se manca o se è più vecchio della 3.5), categoria "LU3G" condivisa con gli altri plugin LU3G e widget "Sponsor Carousel" vuoto, con il segnaposto nell'editor.
- Asset registrati e caricati solo dove c'è il widget; handler frontend di Elementor pronto per gli step successivi.
- Aggiornamenti automatici dalle release di https://github.com/lu3gofficial/lu3g-sponsor-carousel.
