# Registro delle modifiche

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
