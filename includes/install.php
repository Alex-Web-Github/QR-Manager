<?php

/**
 * Création des tables à l’activation du plugin
 */

register_activation_hook(__FILE__, 'qr_manager_install');

/**
 * Fonction d'installation du plugin.
 * Crée les tables nécessaires dans la base de données.
 */
function qr_manager_install()
{
  global $wpdb;

  $charset_collate = $wpdb->get_charset_collate();

  $table_qr = $wpdb->prefix . 'qr_manager_codes';
  $table_hits = $wpdb->prefix . 'qr_manager_hits';

  require_once ABSPATH . 'wp-admin/includes/upgrade.php';

  // Table des QR codes
  $qr_table_sql = "
        CREATE TABLE $table_qr (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            slug VARCHAR(100) NOT NULL UNIQUE,
            label VARCHAR(255) DEFAULT '',
            target_url TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;
    ";
  dbDelta($qr_table_sql);

  // Table des hits
  $hits_table_sql = "
        CREATE TABLE $table_hits (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            qr_id BIGINT UNSIGNED NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            referer TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX (qr_id)
        ) $charset_collate;
    ";
  dbDelta($hits_table_sql);

  // Ajout manuel de la contrainte de clé étrangère (dbDelta ne gère pas bien les FOREIGN KEY)
  $wpdb->query("ALTER TABLE $table_hits
        ADD CONSTRAINT fk_qr_manager_hits_qr_id
        FOREIGN KEY (qr_id) REFERENCES $table_qr(id) ON DELETE CASCADE;");
}
