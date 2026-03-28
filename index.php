<?php
// Entry point for shared hosting where the project root is served as a subdirectory.
// Strips the subdirectory prefix from REQUEST_URI so the app router works correctly.
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($scriptDir && strpos($_SERVER['REQUEST_URI'], $scriptDir) === 0) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($scriptDir));
    if (empty($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = '/';
    }
}
require __DIR__ . '/public/index.php';
