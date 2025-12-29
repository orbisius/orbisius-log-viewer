<?php

if (!defined('ABSPATH')) {
    exit;
}

$debug_constants = [
    'WP_DEBUG' => defined('WP_DEBUG') ? WP_DEBUG : false,
    'WP_DEBUG_LOG' => defined('WP_DEBUG_LOG') ? WP_DEBUG_LOG : false,
    'WP_DEBUG_DISPLAY' => defined('WP_DEBUG_DISPLAY') ? WP_DEBUG_DISPLAY : false,
    'SCRIPT_DEBUG' => defined('SCRIPT_DEBUG') ? SCRIPT_DEBUG : false,
];

?>

<style>
.orbisius-log-viewer-wrapper {
    max-width: 1200px;
}

.orbisius-log-viewer-wrapper .postbox {
    margin-top: 0;
}

.orbisius-log-viewer-wrapper #poststuff .inside {
    padding: 0 12px;
    margin: 0;
}

.orbisius-log-viewer-wrapper .inside {
    margin: 0;
    padding: 20px;
}

.orbisius-log-viewer-extra-title-info {
    float: right;
    font-size: 0.9em;
    color: #666;
}

.orbisius-log-viewer-loading {
    padding: 20px;
    text-align: center;
    color: #666;
}

.orbisius-log-viewer-debug-list {
    margin: 0;
    list-style: none;
}

.orbisius-log-viewer-debug-list li {
    margin-bottom: 8px;
}

.orbisius-log-viewer-debug-enabled {
    color: #46b450;
}

.orbisius-log-viewer-debug-disabled {
    color: #dc3232;
}

.orbisius-log-viewer-debug-description {
    margin-top: 10px;
}

/* Upsell Box Styles */
.orbisius-log-viewer-upsell-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    padding: 25px;
    color: #fff;
    margin-bottom: 20px;
}

.orbisius-log-viewer-upsell-box h3 {
    margin: 0 0 15px 0;
    color: #fff;
    font-size: 1.4em;
    border: none;
    padding: 0;
}

.orbisius-log-viewer-upsell-box p {
    margin: 0 0 15px 0;
    font-size: 1.1em;
    opacity: 0.95;
}

.orbisius-log-viewer-upsell-box ul {
    margin: 0 0 20px 20px;
    list-style: disc;
}

.orbisius-log-viewer-upsell-box ul li {
    margin-bottom: 8px;
    opacity: 0.95;
}

.orbisius-log-viewer-upsell-btn {
    display: inline-block;
    background: #fff;
    color: #667eea;
    padding: 12px 30px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1em;
    transition: all 0.3s ease;
}

.orbisius-log-viewer-upsell-btn:hover {
    background: #f0f0f0;
    color: #764ba2;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Table Styles */
.orbisius-log-viewer-wrapper .widefat {
    margin-top: 10px;
}

.orbisius-log-viewer-wrapper .widefat td {
    vertical-align: middle;
}

.orbisius-log-viewer-download-btn {
    text-decoration: none;
}
</style>

<div class="wrap orbisius-log-viewer-wrapper">
    <h1>Orbisius Log Viewer</h1>

    <!-- Upsell Box -->
    <div class="orbisius-log-viewer-upsell-box">
        <h3>Need More Power? Get Orbisius Log Optimizer!</h3>
        <p>Take control of your WordPress logs with advanced features:</p>
        <ul>
            <li><strong>Delete log files</strong> directly from the dashboard</li>
            <li><strong>Suppress warnings</strong> from plugins, themes, or WP core</li>
            <li><strong>Automatic log rotation</strong> to prevent disk space issues</li>
            <li><strong>Filter errors by path</strong> - include/exclude specific directories</li>
            <li><strong>Debug IP whitelist</strong> - show errors only to specific IPs</li>
            <li><strong>Track suppressed errors</strong> - see how much disk space you're saving</li>
        </ul>
        <a href="https://orbisius.com/products/wordpress-plugins/orbisius-log-optimizer/?utm_source=orbisius-log-viewer&utm_medium=plugin&utm_campaign=upsell&utm_content=top-cta" target="_blank" class="orbisius-log-viewer-upsell-btn">Get Orbisius Log Optimizer</a>
    </div>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h3>
                            <span>Log Files</span>
                            <span class="orbisius-log-viewer-extra-title-info"></span>
                        </h3>
                        <div class="inside">
                            <div id="orbisius-log-viewer-results" class="orbisius-log-viewer-loading">
                                Loading log files...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h3><span>WordPress Debug Status</span></h3>
                        <div class="inside">
                            <?php
                            echo "<ul class='orbisius-log-viewer-debug-list'>\n";
                            foreach ($debug_constants as $const => $value) {
                                $status_class = $value ? 'orbisius-log-viewer-debug-enabled' : 'orbisius-log-viewer-debug-disabled';
                                $status_icon = $value ? '&#10003;' : '&#10007;';

                                if ($const === 'WP_DEBUG_LOG') {
                                    if (is_bool($value) || is_numeric($value)) {
                                        $status_class = $value ? 'orbisius-log-viewer-debug-enabled' : 'orbisius-log-viewer-debug-disabled';
                                        $status_icon = $value ? '&#10003;' : '&#10007;';
                                        $value = $value ? 'Enabled' : 'Disabled';
                                    } elseif (!empty($value)) {
                                        $status_class = 'orbisius-log-viewer-debug-enabled';
                                        $status_icon = '&#10003;';
                                        $filename = basename($value);
                                        $value = 'File: ' . esc_html($filename);
                                    }
                                } else {
                                    $value = $value ? 'Enabled' : 'Disabled';
                                }

                                printf(
                                    "<li><strong>%s:</strong> <span class='%s'>%s %s</span></li>\n",
                                    esc_html($const),
                                    esc_attr($status_class),
                                    $status_icon,
                                    esc_html($value)
                                );
                            }
                            echo "</ul>\n";
                            ?>
                            <p class="description orbisius-log-viewer-debug-description">
                                These settings are defined in wp-config.php
                            </p>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3><span>About</span></h3>
                        <div class="inside">
                            <p>This plugin helps you quickly find and download log files from your WordPress installation.</p>
                            <p>Looking for more features like deleting logs, log rotation, and error suppression?</p>
                            <p><a href="https://orbisius.com/products/wordpress-plugins/orbisius-log-optimizer/?utm_source=orbisius-log-viewer&utm_medium=plugin&utm_campaign=upsell&utm_content=sidebar" target="_blank"><strong>Get Orbisius Log Optimizer</strong></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear" />
    </div>
</div>

<script>
var orbisius_log_viewer = {
    nonce: '<?php echo esc_js(wp_create_nonce('orbisius_log_viewer_ajax_nonce')); ?>',
};

(function($) {
    const MAX_RETRIES = 3;
    const RETRY_DELAY = 2000;
    let retryCount = 0;

    function loadLogFiles() {
        const $results = $('#orbisius-log-viewer-results');
        const $titleInfo = $('.orbisius-log-viewer-extra-title-info');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'orbisius_log_viewer_load_log_files',
                nonce: orbisius_log_viewer.nonce,
            },
            success: function(response) {
                if (response && (response.status === true || response.status === 1 || response.status === '1')) {
                    $results.html(response.data.html);
                    $titleInfo.html('Total Size: ' + response.data.total_size_fmt + ' | Files: ' + response.data.total_files);
                } else {
                    handleError(response?.msg || 'Unknown error occurred');
                }
            },
            error: function(xhr, status, error) {
                handleError(error || 'Network error occurred');
            }
        });
    }

    function handleError(error) {
        const $results = $('#orbisius-log-viewer-results');

        if (retryCount < MAX_RETRIES) {
            retryCount++;
            $results.html('Loading failed. Retrying (' + retryCount + '/' + MAX_RETRIES + ')...');
            setTimeout(loadLogFiles, RETRY_DELAY);
        } else {
            $results.html('Error loading log files: ' + error + '. <a href="#" class="retry-load">Retry</a>');
        }
    }

    $(document).ready(function() {
        loadLogFiles();

        $(document).on('click', '.retry-load', function(e) {
            e.preventDefault();
            retryCount = 0;
            loadLogFiles();
        });
    });
})(jQuery);
</script>
