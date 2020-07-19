<?php
require_once __DIR__ . '/default.php';

$request_uri = parse_url($_SERVER['REQUEST_URI']);
$queryHash = array();
$GLOBALS['requestUri'] = $request_uri['path'];
$GLOBALS['queryStr']   = "search=";
if (array_key_exists('query', $request_uri))
  $GLOBALS['queryStr'] = $request_uri['query'];
if (array_key_exists('query', $request_uri))
  parse_str($request_uri['query'], $queryHash);
if (!array_key_exists('search',$queryHash))
  $queryHash['search']  = "";
if (empty($queryHash['search'])) {
  $GLOBALS['preFilterPlaceHolder'] = "Search in files";
} else {
  $GLOBALS['preFilterPlaceHolder'] = $queryHash['search'];
}
$GLOBALS['queryHash'] = $queryHash;

$request_uri = explode("/", $request_uri['path']);
$script_name = explode("/", dirname($_SERVER['SCRIPT_NAME']));

$app_dir = array();
foreach ($request_uri as $key => $value) {
    if (isset($script_name[$key]) && $script_name[$key] == $value) {
        $app_dir[] = $script_name[$key];
    }
}

define('APP_DIR', rtrim(implode('/', $app_dir), "/"));

$https = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
    $https = true;
}

define('BASE_URL', "http" . ($https ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . APP_DIR);

unset($config_file, $request_uri, $script_name, $app_dir, $https);


if (defined('ACCESS_USER') && defined('ACCESS_PASSWORD')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'login.php';
    Login::instance()->dispatch();
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'wiki.php';

Wiki::instance()->dispatch();
