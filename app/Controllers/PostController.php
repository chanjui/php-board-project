<?php
namespace App\Controllers;

use Core\Database;
use Core\Session;

class PostController {
    private $db;
    private $posts_per_page = 10;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index() {
        try {
            // 페이지네이션
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $this->posts_per_page;

            // 전체 게시글 수 조회
            $total = $this->db->query("SELECT COUNT(*) as count FROM posts")->fetch()['count'];
            $total_pages = ceil($total / $this->posts_per_page);

            // 게시글 목록 조회 (댓글 수 포함)
            $posts = $this->db->query(
                "SELECT p.*, u.username, 
                        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
                 FROM posts p 
                 JOIN users u ON p.user_id = u.id 
                 ORDER BY p.id DESC 
                 LIMIT " . (int)$this->posts_per_page . " OFFSET " . (int)$offset
            )->fetchAll();

            // 뷰 파일 경로
            $viewFile = __DIR__ . '/../Views/posts/index.php';
            $layoutFile = __DIR__ . '/../Views/layouts/main.php';
            
            if (!file_exists($viewFile) || !file_exists($layoutFile)) {
                throw new \Exception("필요한 뷰 파일을 찾을 수 없습니다.");
            }

            // 뷰 렌더링
            ob_start();
            extract([
                'posts' => $posts,
                'current_page' => $page,
                'total_pages' => $total_pages
            ]);
            require $viewFile;
            $content = ob_get_clean();

            // 레이아웃 렌더링
            require $layoutFile;
        } catch (\Exception $e) {
            // 오류 발생 시 500 에러 페이지 표시
            header("HTTP/1.0 500 Internal Server Error");
            echo "서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.";
        }
    }
    
    // 게시글 작성 폼 표시
    public function writeForm() {
        // 로그인 확인
        if (!Session::has('user_id')) {
            Session::flash('error', '로그인이 필요합니다.');
            header('Location: /login');
            exit;
        }
        
        ob_start();
        require __DIR__ . '/../Views/posts/write.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }
    
    // 게시글 작성 처리
    public function write() {
        // 로그인 확인
        if (!Session::has('user_id')) {
            Session::flash('error', '로그인이 필요합니다.');
            header('Location: /login');
            exit;
        }
        
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $user_id = Session::get('user_id');
        
        // 입력값 검증
        if (empty($title) || empty($content)) {
            Session::flash('error', '제목과 내용을 모두 입력해주세요.');
            header('Location: /write');
            exit;
        }
        
        // 게시글 저장
        $this->db->query(
            "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)",
            [$user_id, $title, $content]
        );
        
        Session::flash('success', '게시글이 작성되었습니다.');
        header('Location: /');
        exit;
    }
    
    // 게시글 상세보기
    public function view() {
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            header('Location: /');
            exit;
        }
        
        // 조회수 증가
        $this->db->query(
            "UPDATE posts SET view_count = view_count + 1 WHERE id = ?",
            [$id]
        );
        
        // 게시글 조회
        $post = $this->db->query(
            "SELECT p.*, u.username 
             FROM posts p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.id = ?",
            [$id]
        )->fetch();
        
        if (!$post) {
            Session::flash('error', '존재하지 않는 게시글입니다.');
            header('Location: /');
            exit;
        }
        
        // 댓글 조회
        $comments = $this->db->query(
            "SELECT c.*, u.username 
             FROM comments c 
             JOIN users u ON c.user_id = u.id 
             WHERE c.post_id = ? 
             ORDER BY c.id ASC",
            [$id]
        )->fetchAll();
        
        // 뷰 렌더링
        ob_start();
        extract([
            'post' => $post,
            'comments' => $comments
        ]);
        require __DIR__ . '/../Views/posts/view.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }
    
    // 게시글 수정 폼
    public function editForm() {
        // 로그인 확인
        if (!Session::has('user_id')) {
            Session::flash('error', '로그인이 필요합니다.');
            header('Location: /login');
            exit;
        }
        
        $id = $_GET['id'] ?? 0;
        $user_id = Session::get('user_id');
        
        // 게시글 조회
        $post = $this->db->query(
            "SELECT * FROM posts WHERE id = ? AND user_id = ?",
            [$id, $user_id]
        )->fetch();
        
        if (!$post) {
            Session::flash('error', '수정할 수 없는 게시글입니다.');
            header('Location: /');
            exit;
        }
        
        // 뷰 렌더링
        ob_start();
        extract(['post' => $post]);
        require __DIR__ . '/../Views/posts/edit.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }
    
    // 게시글 수정 처리
    public function edit() {
        // 로그인 확인
        if (!Session::has('user_id')) {
            Session::flash('error', '로그인이 필요합니다.');
            header('Location: /login');
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $user_id = Session::get('user_id');
        
        // 입력값 검증
        if (empty($title) || empty($content)) {
            Session::flash('error', '제목과 내용을 모두 입력해주세요.');
            header('Location: /edit/' . $id);
            exit;
        }
        
        // 게시글 수정
        $result = $this->db->query(
            "UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?",
            [$title, $content, $id, $user_id]
        );
        
        if ($result->rowCount() === 0) {
            Session::flash('error', '수정할 수 없는 게시글입니다.');
            header('Location: /');
            exit;
        }
        
        Session::flash('success', '게시글이 수정되었습니다.');
        header('Location: /view/' . $id);
        exit;
    }
    
    // 게시글 삭제
    public function delete() {
        // 로그인 확인
        if (!Session::has('user_id')) {
            Session::flash('error', '로그인이 필요합니다.');
            header('Location: /login');
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        $user_id = Session::get('user_id');
        
        // 게시글 삭제
        $result = $this->db->query(
            "DELETE FROM posts WHERE id = ? AND user_id = ?",
            [$id, $user_id]
        );
        
        if ($result->rowCount() === 0) {
            Session::flash('error', '삭제할 수 없는 게시글입니다.');
            header('Location: /');
            exit;
        }
        
        Session::flash('success', '게시글이 삭제되었습니다.');
        header('Location: /');
        exit;
    }
} 