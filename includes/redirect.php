<?php

/**
 * Gestion des URLs intermédiaires (slug)
 */

add_action('init', 'qr_manager_add_rewrite_rule');
add_filter('query_vars', 'qr_manager_add_query_vars');
add_action('template_redirect', 'qr_manager_handle_redirect');

/**
 * Ajoute une règle de réécriture pour les URLs de type /qr/{slug}.
 */
function qr_manager_add_rewrite_rule()
{
  add_rewrite_rule('^qr/([^/]+)/?', 'index.php?qr_slug=$matches[1]', 'top');
}

/**
 * Ajoute la variable de requête 'qr_slug'.
 *
 * @param array $vars Les variables de requête existantes.
 * @return array Les variables de requête mises à jour.
 */
function qr_manager_add_query_vars($vars)
{
  $vars[] = 'qr_slug';
  return $vars;
}

/**
 * Gère la redirection basée sur le slug fourni dans l'URL.
 */
function qr_manager_handle_redirect()
{
  $slug = sanitize_title(get_query_var('qr_slug'));
  if (!$slug) {
    return;
  }

  global $wpdb;
  $table_qr = $wpdb->prefix . 'qr_manager_codes';
  $table_hits = $wpdb->prefix . 'qr_manager_hits';

  // Recherche du QR Code correspondant au slug
  $qr = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_qr WHERE slug = %s", $slug));
  if (!$qr) {
    wp_die(esc_html__('QR Code introuvable.', 'qr-manager'));
  }

  // Vérifie la sécurité de l'URL cible
  $target_url = qr_manager_validate_target_url($qr->target_url);
  if (!$target_url) {
    wp_die(esc_html__('Redirection vers un domaine non autorisé.', 'qr-manager'));
  }

  // Enregistre le scan dans la base de données
  qr_manager_log_scan($wpdb, $table_hits, $qr->id);

  // Redirection sécurisée
  wp_redirect($target_url, 302);
  exit;
}

/**
 * Valide l'URL cible pour s'assurer qu'elle appartient au domaine principal ou à ses sous-domaines.
 *
 * @param string $target_url L'URL cible à valider.
 * @return string|false L'URL validée ou false si elle est invalide.
 */
function qr_manager_validate_target_url($target_url)
{
  $target_url = esc_url_raw($target_url);
  $target_host = parse_url($target_url, PHP_URL_HOST);
  $site_host = parse_url(get_site_url(), PHP_URL_HOST);

  // Vérifie si le domaine cible est autorisé
  if ($target_host === $site_host || preg_match('/\.' . preg_quote($site_host) . '$/', $target_host)) {
    return $target_url;
  }

  return false;
}

/**
 * Enregistre les informations du scan dans la base de données.
 *
 * @param wpdb $wpdb L'objet global de la base de données WordPress.
 * @param string $table_hits Le nom de la table des hits.
 * @param int $qr_id L'ID du QR Code scanné.
 */
function qr_manager_log_scan($wpdb, $table_hits, $qr_id)
{
  // Nettoyage des données sensibles
  $ip_address = sanitize_text_field($_SERVER['REMOTE_ADDR']);
  $user_agent = sanitize_textarea_field($_SERVER['HTTP_USER_AGENT'] ?? '');
  // Enregistrer la page d'origine depuis laquelle l'utilisateur a scanné ou cliqué sur le QR code
  $referer = esc_url_raw($_SERVER['HTTP_REFERER'] ?? '');

  // Limiter la longueur des champs pour éviter les abus
  $user_agent = substr($user_agent, 0, 500); // Limite à 500 caractères
  $referer = substr($referer, 0, 1000); // Limite à 1000 caractères

  // Insertion dans la base de données
  $wpdb->insert($table_hits, [
    'qr_id' => intval($qr_id),
    'ip_address' => $ip_address,
    'user_agent' => $user_agent,
    'referer' => $referer,
    'created_at' => current_time('mysql')
  ], ['%d', '%s', '%s', '%s', '%s']);
}
