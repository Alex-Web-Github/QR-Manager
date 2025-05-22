# 📦 QR Manager – Plugin WordPress de gestion de QR codes

**QR Manager** est un plugin WordPress qui permet de générer, afficher, télécharger et suivre les statistiques de QR codes personnalisés (formats PNG & SVG). Applications possibles : campagnes marketing, gestion d’événements etc.

---

## ✨ Fonctionnalités

- ✅ Génération de QR codes au format PNG et SVG
- ✅ Téléchargement direct des QR codes générés
- ✅ Enregistrement et suivi des **scans (hits)** des QR codes
- ✅ Interface simple et intuitive dans le back-office WordPress

---

## 🔧 Installation

1. **Téléchargement**
   - Depuis GitHub :

     ```bash
     git clone https://github.com/votre-utilisateur/qr-manager.git wp-content/plugins/qr-manager
     ```

   - Ou téléversez le dossier `qr-manager` via l’interface d’administration WordPress.

2. **Activation**
   - Activez le plugin depuis le menu **Extensions** de WordPress.

3. **Configuration**
   - Aucun paramétrage initial requis.

---

## 🚀 Utilisation

- Allez dans **QR Manager > Ajouter un QR Code**.
- Saisissez les données (slug, URL, étiquette, etc.).
- Cliquez sur **Générer**.
- Téléchargez le QR code généré en PNG ou SVG.
- Consultez les statistiques de scan dans l'onglet **Voir les statistiques**.

---

## 📂 Structure du plugin

QR-Manager/
├── assets/               # Images, CSS, JS
├── includes/             # Logique du plugin (admin.php, générateurs, hooks…)
├── languages/            # Fichiers de traduction (fichiers .pot/.mo/.po)
├── qrcodes/              # QR codes générés automatiquement (non versionnés)
├── src/                  # Code source additionnel (classes, services…)
├── vendor/               # Librairies installées via Composer
├── composer.json         # Dépendances PHP
├── composer.lock         # Versions figées des dépendances
├── qr-manager.php        # Fichier principal du plugin
└── README.md             # Ce fichier

---

## 📦 Dépendances

Ce plugin repose sur la bibliothèque [endroid/qr-code](https://github.com/endroid/qr-code) (installée via Composer).
