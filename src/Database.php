<?php
namespace SimpleWire;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private $db;

    public function __construct() {
        try {
            $dbPath = __DIR__ . '/../storage/database.sqlite';
            $isNew = !file_exists($dbPath);
            
            $storageDir = dirname($dbPath);
            if (!is_dir($storageDir)) {
                mkdir($storageDir, 0777, true);
            }

            $this->db = new PDO("sqlite:$dbPath");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($isNew) {
                $this->setupDatabase();
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to connect to database: ' . $e->getMessage());
        }
    }

    private function setupDatabase() {
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                message TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    public function saveMessage($name, $email, $message) {
        try {
            $stmt = $this->db->prepare('
                INSERT INTO messages (name, email, message)
                VALUES (:name, :email, :message)
            ');
            
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':message' => $message
            ]);
            
            return true;
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to save message: ' . $e->getMessage());
        }
    }

    public function getMessages() {
        try {
            $stmt = $this->db->query('SELECT * FROM messages ORDER BY created_at DESC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to fetch messages: ' . $e->getMessage());
        }
    }
} 