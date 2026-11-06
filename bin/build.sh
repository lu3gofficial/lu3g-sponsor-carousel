#!/bin/sh
# Crea dist/lu3g-sponsor-carousel.zip (cartella lu3g-sponsor-carousel/ all'interno), il pacchetto che WordPress installa e aggiorna.
# Esclude i file che servono solo allo sviluppo (git, script, automazioni).
set -e
cd "$(dirname "$0")/.."
rm -rf dist && mkdir -p dist/lu3g-sponsor-carousel
cp -R lu3g-sponsor-carousel.php readme.txt includes assets languages dist/lu3g-sponsor-carousel/
(cd dist && zip -rq lu3g-sponsor-carousel.zip lu3g-sponsor-carousel -x '*.DS_Store')
rm -rf dist/lu3g-sponsor-carousel
echo "dist/lu3g-sponsor-carousel.zip"
