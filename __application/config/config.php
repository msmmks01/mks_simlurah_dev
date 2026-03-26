<?php defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set('Asia/Makassar');

/* =========================================================
| BASE URL & INDEX
========================================================= */

$config['base_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$config['base_url'] .= preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME'])) . '/';

$config['index_page'] = 'index.php';
$config['url_suffix'] = '';

/* =========================================================
| URI & ROUTING
========================================================= */

$config['uri_protocol'] = 'AUTO';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

$config['enable_query_strings'] = FALSE;
$config['allow_get_array'] = TRUE;

$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

/* =========================================================
| APPLICATION
========================================================= */

$config['language'] = 'english';
$config['charset']  = 'UTF-8';
$config['subclass_prefix'] = 'JINGGA_';
$config['composer_autoload'] = 'vendor/autoload.php';

$config['enable_hooks'] = FALSE;
$config['rewrite_short_tags'] = FALSE;
$config['time_reference'] = 'local';

/* =========================================================
| LOGGING
========================================================= */

$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';

/* =========================================================
| ERROR & CACHE
========================================================= */

$config['error_views_path'] = '';
$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;
$config['compress_output'] = FALSE;

/* =========================================================
| SECURITY
========================================================= */

$config['encryption_key'] = 'dfALfpwMG98smd764JfpdfCVB0065sgj';

$config['global_xss_filtering'] = FALSE;

/* =========================================================
| SESSION
========================================================= */

$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_simlurah';
$config['sess_expiration'] = 1000000;
$config['sess_save_path'] = sys_get_temp_dir();

$config['sess_match_ip'] = TRUE;
$config['sess_time_to_update'] = 1000000;
$config['sess_regenerate_destroy'] = FALSE;

/* =========================================================
| COOKIE
========================================================= */

$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';

$config['cookie_secure'] = FALSE;
$config['cookie_httponly'] = FALSE;
// $config['cookie_samesite'] = 'Lax';

/* =========================================================
| CSRF
========================================================= */

$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'csrf_token';
$config['csrf_cookie_name'] = 'csrf_cookie';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

/* =========================================================
| NETWORK
========================================================= */

$config['proxy_ips'] = '';

/* =========================================================
| SYSTEM
========================================================= */

$config['standardize_newlines'] = FALSE;

/* =========================================================
| LICENSE SERVER
========================================================= */

$config['srv'] = "http://jingga.co.id/jingga_api/cek_lisensi";
$config['client'] = "PUSKESMASKEMAYORAN";