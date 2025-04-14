<?php
namespace Core;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $host = DB_HOST;
            $name = DB_NAME;
            $user = DB_USER;
            $pass = DB_PASS;

            $this->connection = new \PDO(
                "mysql:host={$host};dbname={$name}",
                $user,
                $pass,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (\PDOException $e) {
            die("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            if (empty($params)) {
                // 파라미터가 없는 경우 직접 실행
                return $this->connection->query($sql);
            } else {
                // 파라미터가 있는 경우 prepared statement 사용
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($params);
                return $stmt;
            }
        } catch (\PDOException $e) {
            die("쿼리 실행 실패: " . $e->getMessage());
        }
    }
} 