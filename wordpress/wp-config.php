<?php
define( 'WP_CACHE', true ); 
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'eakvpjqczj');
/** MySQL database username */
define('DB_USER', 'eakvpjqczj');
/** MySQL database password */
define('DB_PASSWORD', 'Y8Rjc3PHy2');
/** MySQL hostname */
define('DB_HOST', 'localhost');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
/**#@+
 * Authentication Unique Keys and Salts.
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
require('wp-salt.php');
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';
/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('FS_METHOD','direct');
define('WPLANG', '');
define('FS_CHMOD_DIR', (0775 & ~ umask()));
define('FS_CHMOD_FILE', (0664 & ~ umask()));
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_REDIS_CONFIG', [
   'token' => "e279430effe043b8c17d3f3c751c4c0846bc70c97f0eaaea766b4079001c",
   'host' => '127.0.0.1',
   'username' => "eakvpjqczj",
   'password' => "uYb25cYEQr",
   'port' => 6379,
   'database' => "3467", 
   'timeout' => 2.5,
   'read_timeout' => 2.5,
   'split_alloptions' => true,
   'async_flush' => true,
   'client' => 'phpredis', 
   'compression' => 'zstd', 
   'serializer' => 'igbinary', 
   'prefetch' => true, 
   'debug' => false,
   'save_commands' => false,
   'prefix' => "eakvpjqczj:",  
   ] );
define( 'WP_REDIS_DISABLED', false );

// 簡單的 .env 讀取函數
if (!function_exists('byob_load_env_file')) {
    function byob_load_env_file($file_path) {
        if (!file_exists($file_path)) {
            return array();
        }
        $env = array();
        $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // 跳過註解
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            // 解析 KEY=VALUE 格式
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                // 移除引號（如果有的話）
                $value = trim($value, '"\'');
                if (!empty($key)) {
                    $env[$key] = $value;
                }
            }
        }
        return $env;
    }
}

// 載入 .env 檔案（假設 .env 在專案根目錄，wp-config.php 的上一層）
$env_file = dirname(__FILE__) . '/../.env';
$env_vars = byob_load_env_file($env_file);

// 設定 MAPS_JAVASCRIPT_API_KEY（優先從 .env 讀取）
if (!defined('MAPS_JAVASCRIPT_API_KEY')) {
    $maps_key = '';
    if (isset($env_vars['MAPS_JAVASCRIPT_API_KEY'])) {
        $maps_key = $env_vars['MAPS_JAVASCRIPT_API_KEY'];
    } elseif (isset($env_vars['GOOGLE_API_KEY'])) {
        // Fallback 到 GOOGLE_API_KEY
        $maps_key = $env_vars['GOOGLE_API_KEY'];
    }
    
    // 如果 .env 沒有，記錄錯誤（不在 wp-config.php 中使用硬編碼，確保安全性）
    if (empty($maps_key)) {
        error_log('警告：MAPS_JAVASCRIPT_API_KEY 未設定。請在 .env 檔案中設定 MAPS_JAVASCRIPT_API_KEY 或 GOOGLE_API_KEY。');
        // 注意：如果 .env 中沒有設定，地圖功能將無法運作
        // 請確保生產環境的 .env 文件包含正確的 API Key
    }
    
    define('MAPS_JAVASCRIPT_API_KEY', $maps_key);
}

/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
        define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');