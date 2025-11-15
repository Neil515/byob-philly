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
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
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

// 載入 .env 檔案（嘗試多個可能的路徑）
// 根據生產環境路徑：/home/1492233.cloudwaysapps.com/eakvpjqczj/public_html/wp-config.php
// .env 應該在：/home/1492233.cloudwaysapps.com/eakvpjqczj/.env（專案根目錄）

// 嘗試 1：專案根目錄（wp-config.php 的上一層）- 主要路徑
$env_file_path1 = dirname(__FILE__) . '/../.env';
// 嘗試 2：WordPress 根目錄（與 wp-config.php 同層）
$env_file_path2 = dirname(__FILE__) . '/.env';
// 嘗試 3：絕對路徑（專案根目錄）- 針對生產環境
$env_file_path3 = '/home/1492233.cloudwaysapps.com/eakvpjqczj/.env';
// 嘗試 4：根據 wp-config.php 位置動態計算
$wp_config_dir = dirname(__FILE__);
$env_file_path4 = realpath($wp_config_dir . '/..') . '/.env';

// 記錄所有嘗試的路徑到多個位置（確保能看到）
$log_file1 = dirname(__FILE__) . '/../.env-debug.log';
$log_file2 = dirname(__FILE__) . '/.env-debug.log';
$log_content = date('Y-m-d H:i:s') . " UTC - wp-config.php: Checking .env paths\n";
$log_content .= "wp-config.php location: " . __FILE__ . "\n";
$log_content .= "wp-config.php dirname: " . dirname(__FILE__) . "\n";
$log_content .= "Path 1 (relative ../): {$env_file_path1} - " . (file_exists($env_file_path1) ? 'EXISTS' : 'NOT FOUND') . "\n";
$log_content .= "Path 2 (same dir): {$env_file_path2} - " . (file_exists($env_file_path2) ? 'EXISTS' : 'NOT FOUND') . "\n";
$log_content .= "Path 3 (absolute): {$env_file_path3} - " . (file_exists($env_file_path3) ? 'EXISTS' : 'NOT FOUND') . "\n";
$log_content .= "Path 4 (realpath): {$env_file_path4} - " . (file_exists($env_file_path4) ? 'EXISTS' : 'NOT FOUND') . "\n";

// 優先使用存在的文件
$env_file = '';
if (file_exists($env_file_path1)) {
    $env_file = $env_file_path1;
    $log_content .= "✓ Using Path 1 (../.env)\n";
} elseif (file_exists($env_file_path2)) {
    $env_file = $env_file_path2;
    $log_content .= "✓ Using Path 2 (same dir)\n";
} elseif (file_exists($env_file_path3)) {
    $env_file = $env_file_path3;
    $log_content .= "✓ Using Path 3 (absolute)\n";
} elseif (file_exists($env_file_path4)) {
    $env_file = $env_file_path4;
    $log_content .= "✓ Using Path 4 (realpath)\n";
} else {
    $log_content .= "✗ ERROR: No .env file found in any location!\n";
    $log_content .= "Please upload .env file to: " . dirname(__FILE__) . "/../.env\n";
}

// 寫入調試日誌文件（多個位置確保能看到）
@file_put_contents($log_file1, $log_content, FILE_APPEND);
@file_put_contents($log_file2, $log_content, FILE_APPEND);
// 同時寫入到 wp-content 目錄（更容易找到）
$log_file3 = dirname(__FILE__) . '/wp-content/.env-debug.log';
@file_put_contents($log_file3, $log_content, FILE_APPEND);

$env_vars = byob_load_env_file($env_file);

// 設定 MAPS_JAVASCRIPT_API_KEY（從 .env 讀取）
if (!defined('MAPS_JAVASCRIPT_API_KEY')) {
    $maps_key = '';
    
    // 調試信息（寫入多個位置的日誌文件，確保能看到）
    $debug_log1 = dirname(__FILE__) . '/../.env-debug.log';
    $debug_log2 = dirname(__FILE__) . '/.env-debug.log';
    $debug_log3 = dirname(__FILE__) . '/wp-content/.env-debug.log';
    $debug_log = $debug_log1; // 主要日誌文件
    $debug_content = date('Y-m-d H:i:s') . " - MAPS_JAVASCRIPT_API_KEY setup\n";
    $debug_content .= "Env file: " . ($env_file ?: 'NOT SET') . "\n";
    $debug_content .= "Env file exists: " . ($env_file && file_exists($env_file) ? 'YES' : 'NO') . "\n";
    $debug_content .= "Loaded env vars: " . implode(', ', array_keys($env_vars)) . "\n";
    
    if (isset($env_vars['MAPS_JAVASCRIPT_API_KEY'])) {
        $maps_key = $env_vars['MAPS_JAVASCRIPT_API_KEY'];
        $debug_content .= "Using MAPS_JAVASCRIPT_API_KEY from .env\n";
        $debug_content .= "Key length: " . strlen($maps_key) . "\n";
        $debug_content .= "Key preview: " . substr($maps_key, 0, 15) . "...\n";
    } elseif (isset($env_vars['GOOGLE_API_KEY'])) {
        // Fallback 到 GOOGLE_API_KEY
        $maps_key = $env_vars['GOOGLE_API_KEY'];
        $debug_content .= "Using GOOGLE_API_KEY from .env as fallback\n";
        $debug_content .= "Key length: " . strlen($maps_key) . "\n";
        $debug_content .= "Key preview: " . substr($maps_key, 0, 15) . "...\n";
    } else {
        $debug_content .= "ERROR: Neither MAPS_JAVASCRIPT_API_KEY nor GOOGLE_API_KEY found in .env\n";
    }
    
    // 如果 .env 沒有，記錄錯誤
    if (empty($maps_key)) {
        $debug_content .= "ERROR: MAPS_JAVASCRIPT_API_KEY is empty!\n";
        $debug_content .= "Available keys in .env: " . implode(', ', array_keys($env_vars)) . "\n";
        $debug_content .= "WARNING: No API Key found from .env file!\n";
        // 注意：根據檢查結果，.env 文件存在於 Path 2，應該能讀取
        // 如果這裡顯示錯誤，可能是 byob_load_env_file() 函數有問題
    }
    
    $debug_content .= "Final maps_key length: " . strlen($maps_key) . "\n";
    $debug_content .= "Final maps_key preview: " . substr($maps_key, 0, 15) . "...\n";
    $debug_content .= "---\n";
    
    // 寫入調試日誌（多個位置）
    @file_put_contents($debug_log1, $debug_content, FILE_APPEND);
    @file_put_contents($debug_log2, $debug_content, FILE_APPEND);
    @file_put_contents($debug_log3, $debug_content, FILE_APPEND);
    
    // 同時嘗試 error_log（如果可用）
    @error_log('BYOB: MAPS_JAVASCRIPT_API_KEY setup - File: ' . ($env_file ?: 'NONE') . ' - Key length: ' . strlen($maps_key));
    
    define('MAPS_JAVASCRIPT_API_KEY', $maps_key);
}

/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
        define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');