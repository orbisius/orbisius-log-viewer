<?php

defined('ABSPATH') || exit();

class Orbisius_Log_Viewer_Util {
    /**
     * Common log file patterns
     * @var array
     */
    private static $log_patterns = [
        '/\.log[\-\.\w]*$/i',
        '/\.log[-_]\d{6,8}(\.(gz|bz2|zip|7z|rar|xz|lz4|zst))?$/i',
        '/(?:error|debug|php|server|access|wordpress)[-_]?log[\-\.\w]*$/i',
        '/\.\d{6,8}(\.(gz|bz2|zip|7z|rar|xz|lz4|zst))?$/i',
        '/.*error[-_]?log[^\/]*$/i',
        '/\.ht.*error[-_]?log[^\/]*$/i',
    ];

    /**
     * Format file size into human readable format
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    public static function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get path relative to WordPress root
     * @param string $path Full file path
     * @return string Relative path
     */
    public static function getRelativePath($path) {
        $path = str_replace('\\', '/', $path);
        $abspath = str_replace('\\', '/', ABSPATH);

        $relative_path = str_replace($abspath, '/', $path);

        return '/' . ltrim($relative_path, '/');
    }

    /**
     * Get all log file patterns including PHP's configured error_log
     * @return array Array of regex patterns for log files
     */
    public static function getLogPatterns() {
        $patterns = self::$log_patterns;

        $error_log_path = ini_get('error_log');

        if (!empty($error_log_path)) {
            $error_log_name = basename($error_log_path);

            if (!empty($error_log_name) && $error_log_name != '.') {
                $patterns[] = '/' . preg_quote($error_log_name, '/') . '[\.\w]*$/i';
            }
        }

        return $patterns;
    }

    /**
     * Scan for log files in WordPress installation
     * @param string $dir Optional directory to scan, defaults to ABSPATH
     * @return array Array of log files with their details
     */
    public static function scanLogFiles($dir = ''): array {
        $log_files = [];

        try {
            $scan_dir = !empty($dir) ? $dir : ABSPATH;
            $log_files = self::scanDirectory($scan_dir);

            // Also scan wp-content if it's outside ABSPATH
            if (empty($dir)
                && defined('WP_CONTENT_DIR')
                && (strpos(WP_CONTENT_DIR, ABSPATH) === false)) {
                $wp_content_files = self::scanDirectory(WP_CONTENT_DIR);
                $log_files = array_merge($log_files, $wp_content_files);
            }
        } catch (Exception $e) {
            error_log('Orbisius Log Viewer: Error scanning for log files: ' . $e->getMessage());
        }

        return $log_files;
    }

    /**
     * Recursively scan directory for log files
     * @param string $dir Directory to scan
     * @param int $depth Current depth level
     * @return array Found log file paths
     */
    private static function scanDirectory(string $dir, int $depth = 0): array {
        $results = [];
        $max_depth = 7;
        $seen_files = [];
        $skip_ext = [
            'php', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'ico',
            'html', 'htm', 'css', 'js', 'woff', 'woff2', 'ttf',
            'svg', 'eot', 'otf',
        ];

        if ($depth > $max_depth) {
            return $results;
        }

        try {
            $dir_iterator = new RecursiveDirectoryIterator(
                $dir,
                RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS
            );

            $log_patterns = self::getLogPatterns();

            $filter_iterator = new RecursiveCallbackFilterIterator(
                $dir_iterator,
                function ($file, $key, $iterator) use ($log_patterns, $skip_ext, &$seen_files) {
                    $skip_dirs = ['.git', '.svn', 'vendor', 'node_modules'];

                    if ($iterator->hasChildren()) {
                        return !in_array($file->getBasename(), $skip_dirs);
                    }

                    $full_path = $file->getPathname();
                    $file_hash = crc32($full_path);

                    if (isset($seen_files[$file_hash])) {
                        return false;
                    }

                    $seen_files[$file_hash] = 1;

                    foreach ($log_patterns as $pattern) {
                        $base_name = $file->getBasename();
                        $ext = pathinfo($base_name, PATHINFO_EXTENSION);

                        if (empty($ext) || in_array($ext, $skip_ext)) {
                            return false;
                        }

                        if (preg_match($pattern, $base_name)) {
                            return true;
                        }
                    }

                    return false;
                }
            );

            $iterator = new RecursiveIteratorIterator(
                $filter_iterator,
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );

            $remaining_depth = $max_depth - $depth;
            $remaining_depth = $remaining_depth <= 0 ? 0 : $remaining_depth;
            $iterator->setMaxDepth($remaining_depth);

            foreach ($iterator as $file) {
                if (!$file->isFile() || !is_readable($file->getRealPath())) {
                    continue;
                }

                $file_path = $file->getRealPath();
                $size = filesize($file_path);

                $results[] = [
                    'path' => self::getRelativePath($file_path),
                    'size' => $size,
                    'size_fmt' => self::formatFileSize($size),
                ];
            }

        } catch (Exception $e) {
            error_log('Orbisius Log Viewer: Error scanning directory: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Generates a reliable partial hash for any input
     * @param mixed $input Data to hash
     * @param int $length Length of the hash to return
     * @return string Partial hash of the input
     */
    public static function calcPartialHash($input, $length = 12) {
        if (!is_string($input)) {
            $input = serialize($input);
        }

        $full_hash = sha1($input);

        return substr($full_hash, 0, $length);
    }
}
