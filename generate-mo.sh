#!/bin/zsh
# Génère tous les fichiers .mo à partir des .po dans le dossier languages/

cd "$(dirname "$0")/languages" || exit 1

for f in *.po; do
  if [[ -f "$f" ]]; then
    msgfmt -o "${f%.po}.mo" "$f"
    echo "Généré : ${f%.po}.mo"
  fi
done
