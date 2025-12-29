<?php

defined('ABSPATH') || exit();

class Orbisius_Log_Viewer_Admin {
    /**
     * Get the single instance of the class (Singleton pattern).
     */
    public static function getInstance() {
        static $instance = null;

        // This will make the calling class to be instantiated.
        // no need each sub class to define this method.
        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Install hooks for the admin functionality.
     */
    public function installHooks() {
        add_action('admin_menu', [$this, 'addMenuPages']);
        add_action('wp_ajax_orbisius_log_viewer_load_log_files', [$this, 'loadLogFilesAjax']);
        add_action('wp_ajax_orbisius_log_viewer_download_log_file', [$this, 'downloadLogFileAjax']);
    }

    /**
     * Add the plugin pages to WordPress admin menu.
     */
    public function addMenuPages() {
        // Add to Settings menu
        add_options_page(
            'Orbisius Log Viewer',
            'Orbisius Log Viewer',
            'manage_options',
            'orbisius-log-viewer',
            [$this, 'renderSettingsPage']
        );

        // Add to Tools menu
        add_management_page(
            'Orbisius Log Viewer',
            'Orbisius Log Viewer',
            'manage_options',
            'orbisius-log-viewer-tool',
            [$this, 'renderSettingsPage']
        );
    }

    /**
     * Render the settings page.
     */
    public function renderSettingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        include __DIR__ . '/admin-settings.php';
    }

    /**
     * Handle AJAX request for log files
     */
    public function loadLogFilesAjax(): void {
        $response = [
            'status' => false,
            'msg' => '',
            'data' => [
                'html' => '',
                'total_files' => 0,
                'total_size_fmt' => '0 B',
            ],
        ];

        try {
            if (!current_user_can('manage_options')) {
                throw new Exception('Insufficient permissions');
            }

            check_ajax_referer('orbisius_log_viewer_ajax_nonce', 'nonce');

            $log_files = Orbisius_Log_Viewer_Util::scanLogFiles();

            // Sort files by size in descending order
            usort($log_files, function($a, $b) {
                return $b['size'] <=> $a['size'];
            });

            $total_size = array_sum(array_column($log_files, 'size'));

            // Generate table HTML
            $html = '<table class="widefat striped">';
            $html .= '<thead><tr><th>File</th><th>Size</th><th>Actions</th></tr></thead>';
            $html .= '<tbody>';

            if (empty($log_files)) {
                $html .= '<tr><td colspan="3">No log files found.</td></tr>';
            } else {
                foreach ($log_files as $file) {
                    $file_path = esc_attr($file['path']);
                    $file_path_display = esc_html($file['path']);
                    $file_size = esc_html($file['size_fmt']);
                    $download_url = wp_nonce_url(
                        admin_url('admin-ajax.php?action=orbisius_log_viewer_download_log_file&file=' . urlencode($file['path'])),
                        'orbisius_log_viewer_download_' . $file['path']
                    );

                    $html .= '<tr>';
                    $html .= '<td>' . $file_path_display . '</td>';
                    $html .= '<td>' . $file_size . '</td>';
                    $html .= '<td>';
                    $html .= '<a href="' . esc_url($download_url) . '" class="button button-secondary orbisius-log-viewer-download-btn">Download</a>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            }

            $html .= '</tbody></table>';

            $response['status'] = true;
            $response['msg'] = 'Log files loaded successfully';
            $response['data'] = [
                'html' => $html,
                'total_files' => count($log_files),
                'total_size_fmt' => Orbisius_Log_Viewer_Util::formatFileSize($total_size),
            ];

        } catch (Exception $e) {
            $response['msg'] = $e->getMessage();
        }

        wp_send_json($response);
    }

    /**
     * Handle log file download
     */
    public function downloadLogFileAjax(): void {
        try {
            if (!current_user_can('manage_options')) {
                throw new Exception('Insufficient permissions');
            }

            $file = '';

            if (!empty($_REQUEST['file'])) {
                $file = $_REQUEST['file'];
                $file = strip_tags($file);
                $file = trim($file);
            }

            if (empty($file)) {
                throw new Exception('No file specified');
            }

            // Verify nonce
            if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'orbisius_log_viewer_download_' . $file)) {
                throw new Exception('Security check failed');
            }

            // Clean path by removing multiple slashes and ../
            while (strpos($file, '..') !== false) {
                $file = str_replace('..', '', $file);
            }

            $file = str_replace('//', '/', $file);
            $file = ltrim($file, '/');

            // Verify it matches log file pattern
            $is_valid = false;
            $log_patterns = Orbisius_Log_Viewer_Util::getLogPatterns();

            foreach ($log_patterns as $pattern) {
                if (preg_match($pattern, basename($file))) {
                    $is_valid = true;
                    break;
                }
            }

            if (!$is_valid) {
                throw new Exception('Invalid log file');
            }

            $full_path = ABSPATH . $file;

            if (!file_exists($full_path)) {
                throw new Exception('File not found');
            }

            if (!is_readable($full_path)) {
                throw new Exception('File not readable');
            }

            // Send file for download
            $filename = basename($full_path);
            $filesize = filesize($full_path);

            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . $filesize);
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($full_path);
            exit;

        } catch (Exception $e) {
            wp_die($e->getMessage());
        }
    }
}
