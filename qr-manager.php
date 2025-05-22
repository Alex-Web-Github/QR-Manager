<?php

/**
 * Plugin Name:       QR Manager
 * Plugin URI:        https://example.com/qr-manager
 * Description:       Un plugin WordPress pour créer des QR codes, rediriger vers une URL cible, et suivre les statistiques de flash.
 * Version:           1.0.0
 * Author:            Alexandre Foulc
 * Author URI:        https://example.com
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       qr-manager
 * Domain Path:       /languages
 * @package           QR_Manager
 */

if (!defined('ABSPATH')) {
  exit; // Empêche l'accès direct
}

// Définir les constantes du plugin
define('QR_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('QR_MANAGER_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Inclure la fonction d'installation (création des tables)
require_once QR_MANAGER_PLUGIN_PATH . 'includes/install.php';
register_activation_hook(__FILE__, 'qr_manager_install');

// Charger les fichiers du plugin
function qr_manager_includes()
{
  $includes = [
    'includes/redirect.php',
    'includes/admin.php',
    'includes/class-qr-manager.php',
  ];

  foreach ($includes as $file) {
    $path = QR_MANAGER_PLUGIN_PATH . $file;
    if (file_exists($path)) {
      require_once $path;
    } else {
      error_log("QR Manager error: Le fichier {$file} est introuvable.");
    }
  }
}
add_action('plugins_loaded', 'qr_manager_includes', 5);

// Initialiser le plugin
function run_qr_manager()
{
  if (class_exists('QR_Manager')) {
    $plugin = new QR_Manager();
    $plugin->run();
  } else {
    error_log('QR Manager error: La classe QR_Manager est introuvable.');
  }
}
add_action('plugins_loaded', 'run_qr_manager', 10);
