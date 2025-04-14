<?php

require_once __DIR__ . '/../vendor/autoload.php'; // composer autoload
require_once __DIR__ . '/../config/config.php';   // DB 정보 등 설정

use Core\Router;

// 라우터 인스턴스 생성 & 요청 처리
$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);
