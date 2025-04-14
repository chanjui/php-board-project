<?php

// 에러 표시 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php'; // composer autoload

// config 파일 로드 (환경 변수 포함)
require_once __DIR__ . '/../config/config.php';

require_once __DIR__ . '/../app/Helpers/Session.php';

use Core\Router;
use Core\Session;

// 세션 시작
Session::start();

// 데이터베이스 연결 초기화
$db = Core\Database::getInstance();

// 라우터 인스턴스 생성 & 요청 처리
$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);
