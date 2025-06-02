<?php

/**
 * Interface admin pour créer les QR codes et voir les statistiques.
 */

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

require_once QR_MANAGER_PLUGIN_PATH . 'vendor/autoload.php';

add_action('admin_menu', function () {
  add_menu_page(
    'QR Manager',
    'QR Manager',
    'manage_options',
    'qr-manager',
    'qr_manager_admin_page',
    'dashicons-share-alt',
    30
  );
});

function qr_manager_admin_page()
{
  if (!current_user_can('manage_options')) {
    wp_die(__('Vous n’avez pas la permission d’accéder à cette page.', 'qr-manager'));
  }

  global $wpdb;
  $table_qr = $wpdb->prefix . 'qr_manager_codes';
  $table_hits = $wpdb->prefix . 'qr_manager_hits';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_qr_id'])) {
      $wpdb->delete($table_qr, ['id' => intval($_POST['delete_qr_id'])]);
    } else {
      qr_manager_handle_form_submission($wpdb, $table_qr);
    }
  }

  $qrs = $wpdb->get_results("SELECT * FROM $table_qr ORDER BY id DESC");
  $stats = $wpdb->get_results("
        SELECT q.label, COUNT(h.id) AS hits
        FROM $table_qr q
        LEFT JOIN $table_hits h ON q.id = h.qr_id
        GROUP BY q.id
        ORDER BY hits DESC
    ");
?>
  <div class="wrap">
    <h1><?php esc_html_e('QR Manager', 'qr-manager'); ?></h1>

    <h2 class="nav-tab-wrapper">
      <a href="#add" class="nav-tab nav-tab-active"><?php esc_html_e('Ajouter un QR Code', 'qr-manager'); ?></a>
      <a href="#stats" class="nav-tab"><?php esc_html_e('Voir les Statistiques', 'qr-manager'); ?></a>
    </h2>

    <div id="add" class="qr-tab-content">
      <form method="post">
        <?php wp_nonce_field('qr_manager_add_qr', 'qr_manager_nonce'); ?>
        <input type="hidden" name="qr_manager_form_submitted" value="1" />

        <table class="form-table">
          <tr>
            <th><label for="label"><?php esc_html_e('Nom', 'qr-manager'); ?></label></th>
            <td><input type="text" id="label" name="label" required class="regular-text"></td>
          </tr>
          <tr>
            <th><label for="slug"><?php esc_html_e('Slug (URL courte)', 'qr-manager'); ?></label></th>
            <td><input type="text" id="slug" name="slug" required class="regular-text"></td>
          </tr>
          <tr>
            <th><label for="target_url"><?php esc_html_e('URL cible', 'qr-manager'); ?></label></th>
            <td><input type="url" id="target_url" name="target_url" required class="regular-text"></td>
          </tr>
        </table>

        <?php submit_button(__('Ajouter', 'qr-manager')); ?>
      </form>

      <h2><?php esc_html_e('QR Codes existants', 'qr-manager'); ?></h2>
      <table class="widefat striped">
        <thead>
          <tr>
            <th><?php esc_html_e('Nom', 'qr-manager'); ?></th>
            <th><?php esc_html_e('Slug', 'qr-manager'); ?></th>
            <th><?php esc_html_e('QR PNG', 'qr-manager'); ?></th>
            <th><?php esc_html_e('QR SVG', 'qr-manager'); ?></th>
            <th><?php esc_html_e('Actions', 'qr-manager'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($qrs as $qr): ?>
            <tr>
              <td><?php echo esc_html($qr->label); ?></td>
              <td><?php echo esc_html($qr->slug); ?></td>
              <td><a href="<?php echo esc_url(plugins_url("../qrcodes/{$qr->slug}.png", __FILE__)); ?>" download>PNG</a></td>
              <td><a href="<?php echo esc_url(plugins_url("../qrcodes/{$qr->slug}.svg", __FILE__)); ?>" download>SVG</a></td>
              <td>
                <form method="post" style="display:inline-block;">
                  <input type="hidden" name="delete_qr_id" value="<?php echo intval($qr->id); ?>">
                  <?php submit_button(__('Supprimer', 'qr-manager'), 'delete', '', false); ?>
                </form>
                <a class="button" href="<?php echo esc_url(home_url('/qr/' . $qr->slug)); ?>" target="_blank"><?php esc_html_e('Tester la Redirection', 'qr-manager'); ?></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div id="stats" class="qr-tab-content" style="display:none;">
      <h2><?php esc_html_e('Statistiques des Flashs', 'qr-manager'); ?></h2>
      <table class="widefat striped">
        <thead>
          <tr>
            <th><?php esc_html_e('Nom du QR Code', 'qr-manager'); ?></th>
            <th><?php esc_html_e('Nombre de flashs', 'qr-manager'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($stats as $stat): ?>
            <tr>
              <td><?php echo esc_html($stat->label); ?></td>
              <td><?php echo intval($stat->hits); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <script>
      document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
          e.preventDefault();
          document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
          document.querySelectorAll('.qr-tab-content').forEach(c => c.style.display = 'none');
          tab.classList.add('nav-tab-active');
          document.querySelector(tab.getAttribute('href')).style.display = 'block';
        });
      });
    </script>
  </div>
<?php
}

function qr_manager_handle_form_submission($wpdb, $table_qr)
{
  if (!isset($_POST['qr_manager_form_submitted']) || !check_admin_referer('qr_manager_add_qr', 'qr_manager_nonce')) {
    return;
  }

  $label = sanitize_text_field($_POST['label'] ?? '');
  $slug = sanitize_title($_POST['slug'] ?? '');
  $target_url = esc_url_raw($_POST['target_url'] ?? '');

  $errors = qr_manager_validate_form($label, $slug, $target_url);

  $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_qr WHERE slug = %s", $slug));
  if ($exists > 0) {
    $errors[] = __('Ce slug est déjà utilisé.', 'qr-manager');
  }

  if (!empty($errors)) {
    foreach ($errors as $error) {
      echo '<div class="notice notice-error"><p>' . esc_html($error) . '</p></div>';
    }
    return;
  }

  $wpdb->insert($table_qr, [
    'label' => $label,
    'slug' => $slug,
    'target_url' => $target_url
  ]);

  // === Génération du QR Code ===
  $qr_url = home_url('/qr/' . $slug);
  $plugin_dir = dirname(plugin_dir_path(__FILE__)); // remonte à la racine du plugin
  $upload_dir = $plugin_dir . '/qrcodes/';

  $png_path = $upload_dir . $slug . '.png';
  $svg_path = $upload_dir . $slug . '.svg';

  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
  }

  try {
    // PNG
    $builderPng = new Builder(
      writer: new PngWriter(),
      writerOptions: [],
      validateResult: false,
      data: $qr_url,
      encoding: new Encoding('UTF-8'),
      errorCorrectionLevel: ErrorCorrectionLevel::High,
      size: 300,
      margin: 10,
      roundBlockSizeMode: RoundBlockSizeMode::Margin,
      labelText: "Flashez-moi pour plus d'infos !",
      labelFont: new OpenSans(16),
      labelAlignment: LabelAlignment::Center
    );

    $resultPng = $builderPng->build();
    $resultPng->saveToFile($png_path);

    // SVG
    $builderSvg = new Builder(
      writer: new SvgWriter(),
      writerOptions: [],
      validateResult: false,
      data: $qr_url,
      encoding: new Encoding('UTF-8'),
      errorCorrectionLevel: ErrorCorrectionLevel::High,
      size: 300,
      margin: 10,
      roundBlockSizeMode: RoundBlockSizeMode::Margin,
      labelText: "Flashez-moi pour plus d'infos !",
      labelFont: new OpenSans(16),
      labelAlignment: LabelAlignment::Center
    );

    $resultSvg = $builderSvg->build();
    $resultSvg->saveToFile($svg_path);

    echo '<div class="notice notice-success"><p>' . esc_html__('QR Code PNG et SVG générés avec succès.', 'qr-manager') . '</p></div>';
  } catch (Throwable $e) {
    echo '<div class="notice notice-error"><p>' . esc_html__('Erreur lors de la génération :', 'qr-manager') . ' ' . esc_html($e->getMessage()) . '</p></div>';
  }
}

function qr_manager_validate_form($label, $slug, $target_url)
{
  $errors = [];

  if (!$label || !$slug || !$target_url) {
    $errors[] = __('Tous les champs sont obligatoires.', 'qr-manager');
  } elseif (strlen($slug) > 100) {
    $errors[] = __('Le slug ne doit pas dépasser 100 caractères.', 'qr-manager');
  } elseif (!filter_var($target_url, FILTER_VALIDATE_URL)) {
    $errors[] = __('URL cible invalide.', 'qr-manager');
  }

  return $errors;
}
