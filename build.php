<?php
/* Static exporter: php build.php <page>  → prints the page as flat HTML */
define('STATIC_BUILD', true);
$page = $argv[1] ?? 'index';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [];
ob_start();
require __DIR__ . '/' . $page . '.php';
echo ob_get_clean();
