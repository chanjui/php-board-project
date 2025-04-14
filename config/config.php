<?php

$env = parse_ini_file(__DIR__ . '/../.env'); // 환경변수 파일 읽기

define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);
