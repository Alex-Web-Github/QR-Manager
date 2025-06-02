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

1. **Cloner le dépôt dans le dossier `wp-content/plugins`** :

   ```bash
   git clone https://github.com/Alex-Web-Github/QR-Manager.git wp-content/plugins/qr-manager
   ```

2. **Installer les dépendances PHP avec Composer**

   ```bash
   cd wp-content/plugins/qr-manager
   composer install
   ```

3. **Activation**

   - Activez le plugin depuis le menu **Extensions** de WordPress

4. **Configuration**
   - Aucun paramétrage initial requis.

---

## 🚀 Utilisation

- Allez dans **QR Manager > Ajouter un QR Code**.
- Saisissez les données (slug, URL, étiquette, etc.).
- Cliquez sur **Générer**.
- Téléchargez le QR code généré en PNG ou SVG.
- Consultez les statistiques de scan dans l'onglet **Voir les statistiques**.

---

## 📦 Dépendances

Ce plugin repose sur la bibliothèque [endroid/qr-code](https://github.com/endroid/qr-code) (installée via Composer).

---

## 📜 Licence

Distribué sous la licence GPLv2 ou ultérieure.

---

## 🤝 Contribution

Les contributions sont les bienvenues : issues, pull requests, suggestions !

---
