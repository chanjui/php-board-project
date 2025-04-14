<?php
namespace App\Controllers;

use Core\Database;
use Core\Session;

class UserController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // 로그인 페이지 표시
    public function loginForm() {
        ob_start();
        require __DIR__ . '/../Views/users/login.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    // 로그인 처리
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::flash('error', '이메일과 비밀번호를 모두 입력해주세요.');
            header('Location: /login');
            exit;
        }

        $user = $this->db->query(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        )->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            Session::flash('error', '이메일 또는 비밀번호가 올바르지 않습니다.');
            header('Location: /login');
            exit;
        }

        // 로그인 성공
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::flash('success', '로그인되었습니다.');
        header('Location: /');
        exit;
    }

    // 회원가입 페이지 표시
    public function registerForm() {
        ob_start();
        require __DIR__ . '/../Views/users/register.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }

    // 회원가입 처리
    public function register() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // 입력값 검증
        if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
            Session::flash('error', '모든 필드를 입력해주세요.');
            header('Location: /register');
            exit;
        }

        if ($password !== $password_confirm) {
            Session::flash('error', '비밀번호가 일치하지 않습니다.');
            header('Location: /register');
            exit;
        }

        // 이메일 중복 확인
        $existing = $this->db->query(
            "SELECT COUNT(*) as count FROM users WHERE email = ?",
            [$email]
        )->fetch();

        if ($existing['count'] > 0) {
            Session::flash('error', '이미 사용 중인 이메일입니다.');
            header('Location: /register');
            exit;
        }

        // 사용자명 중복 확인
        $existing = $this->db->query(
            "SELECT COUNT(*) as count FROM users WHERE username = ?",
            [$username]
        )->fetch();

        if ($existing['count'] > 0) {
            Session::flash('error', '이미 사용 중인 사용자명입니다.');
            header('Location: /register');
            exit;
        }

        // 비밀번호 해시화
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 사용자 등록
        $this->db->query(
            "INSERT INTO users (username, email, password) VALUES (?, ?, ?)",
            [$username, $email, $hashedPassword]
        );

        Session::flash('success', '회원가입이 완료되었습니다. 로그인해주세요.');
        header('Location: /login');
        exit;
    }

    // 로그아웃
    public function logout() {
        Session::clear();
        header('Location: /');
        exit;
    }
} 