<?php

/**
 * Classe principale du plugin QR Manager.
 */

if (!defined('ABSPATH')) {
  exit; // Empêche l'accès direct au fichier
}

if (!class_exists('QR_Manager')) {
  class QR_Manager
  {
    /**
     * Constructeur de la classe.
     */
    public function __construct()
    {
      // Initialiser le plugin
      add_action('plugins_loaded', array($this, 'init'));
    }

    /**
     * Méthode d'initialisation du plugin.
     */
    public function init()
    {
      // Charger les fichiers de langue
      $this->load_textdomain();

      // Enregistrer les scripts et styles
      add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Charger les fichiers de langue.
     */
    private function load_textdomain()
    {
      load_plugin_textdomain(
        'qr-manager',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
      );
    }

    /**
     * Méthode pour enregistrer les scripts et styles.
     */
    public function enqueue_assets()
    {
      wp_enqueue_style(
        'qr-manager-style',
        QR_MANAGER_PLUGIN_URL . 'assets/css/style.css',
        array(),
        filemtime(QR_MANAGER_PLUGIN_PATH . 'assets/css/style.css')
      );

      wp_enqueue_script(
        'qr-manager-script',
        QR_MANAGER_PLUGIN_URL . 'assets/js/script.js',
        array('jquery'),
        filemtime(QR_MANAGER_PLUGIN_PATH . 'assets/js/script.js'),
        true
      );
    }

    /**
     * Méthode pour exécuter le plugin (ajoutée pour résoudre l'erreur "Undefined method 'run'").
     */
    public function run()
    {
      // Logique d'initialisation supplémentaire si nécessaire
      error_log('QR_Manager plugin is running.');
    }
  }
}
