<?php

$force = [
    'CACHE_STORE' => 'array',
    'SESSION_DRIVER' => 'database',
    'LOG_CHANNEL' => 'stderr',
    'VIEW_COMPILED_PATH' => '/tmp',
    'QUEUE_CONNECTION' => 'sync',
];

foreach ($force as $k => $v) {
    putenv("$k=$v");
    $_ENV[$k] = $v;
    $_SERVER[$k] = $v;
}

require __DIR__ . '/../public/index.php';