# Orbisius Log Viewer

**Free WordPress plugin to view and download log files from your WordPress installation.**

See what's eating your disk space. Discover hidden log files like debug.log, error_log, and other logs that silently grow and consume your storage.

## Why Monitor Your Log Files?

Log files can grow silently until they fill your disk quota. Many hosting providers will block access to your site when you exceed your storage limit. Knowing your log file sizes helps you prevent unexpected downtime.

## Features

- **Automatic Scanning** - Scans your entire WordPress directory for log files
- **Detects All Log Types** - Finds debug.log, error_log, php_error.log, rotated logs, and compressed archives
- **Human-Readable Sizes** - File sizes displayed in KB, MB, GB format
- **One-Click Download** - Download any log file directly from your dashboard
- **Debug Status Display** - Shows WP_DEBUG, WP_DEBUG_LOG, and other debug constants
- **Dual Menu Access** - Available from both Settings and Tools menu
- **Lightweight** - No bloat, no external dependencies
- **Admin Only** - Only administrators can view and download logs

## Installation

### From GitHub (Direct Download)

1. Download the latest version: [Download ZIP](https://github.com/orbisius/orbisius-log-viewer/archive/refs/heads/main.zip)
2. In WordPress, go to **Plugins > Add New > Upload Plugin**
3. Upload the ZIP file and click **Install Now**
4. Activate the plugin

### Manual Installation

1. Download and unzip the plugin
2. Upload the `orbisius-log-viewer` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress

## Usage

After activation, access the plugin from:
- **Settings > Orbisius Log Viewer**
- **Tools > Orbisius Log Viewer**

The plugin will automatically scan your WordPress installation and display all found log files with their sizes.

## Screenshots

*Coming soon*

## FAQ

### Why is it important to monitor log files?

Log files can grow silently until they fill your disk quota. Many hosting providers will block access to your site when you exceed your storage limit.

### What log files does this plugin find?

It scans for debug.log, error_log, php_error.log, and any file with .log extension. It also detects rotated logs and compressed archives.

### Will it slow down my site?

No. The plugin only runs when you visit its admin page. It doesn't add any code to your frontend or run any background processes.

### Can I delete log files with this plugin?

This free version is for viewing and downloading only. If you need to delete logs, manage log rotation, or suppress errors, check out [Orbisius Log Optimizer](https://orbisius.com/products/wordpress-plugins/orbisius-log-optimizer/).

### Does it work with multisite?

Yes, it works on WordPress multisite installations.

### Where does it scan for logs?

It scans your entire WordPress directory including wp-content, plugins, themes, and uploads. It skips vendor and node_modules folders for performance.

### Why is this plugin free? Is there a catch?

No catch. We offer this plugin as a lead magnet for [Orbisius Log Optimizer](https://orbisius.com/products/wordpress-plugins/orbisius-log-optimizer/), which has more features like deleting logs, log rotation, and error suppression. But this free plugin is fully functional with no limits.

## Need More Features?

Check out [Orbisius Log Optimizer](https://orbisius.com/products/wordpress-plugins/orbisius-log-optimizer/) for advanced features:

- Delete log files directly from dashboard
- Automatic log rotation
- Suppress warnings from plugins, themes, or WP core
- Filter errors by path (include/exclude directories)
- Debug IP whitelist
- Track suppressed errors and disk space savings

## Support

Found a bug or have a feature request? [Submit an issue](https://github.com/orbisius/orbisius-log-viewer/issues)

## License

GPL v2 or later

## Author

[Orbisius](https://orbisius.com)
