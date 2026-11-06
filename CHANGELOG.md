# Registro delle modifiche

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
