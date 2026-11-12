<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';
$_SESSION = [];
session_destroy();
header('Location: ' . u('login'));
exit;
