<?php
namespace Core;

class Router {
    public function dispatch($uri) {
        $uri = parse_url($uri, PHP_URL_PATH);

        // 루트로 들어오면 게시판 메인 출력
        if ($uri === '/' || $uri === '') {
            require_once __DIR__ . '/../app/Controllers/PostController.php';
            $controller = new \App\Controllers\PostController();
            $controller->index();
        }

        // TODO: /write, /view 등 라우팅 추가 가능
    }
}
