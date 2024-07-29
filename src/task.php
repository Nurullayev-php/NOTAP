<?php

declare(strict_types=1);
require "src/DB.php";
class Task
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function add(string $text, int $userId=1)
    {

        $stmt   = $this->pdo->prepare("INSERT INTO `note-app` (text, created_at, userId) VALUES (:text, NOW(), :userId)");
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    public function getAll(): false|array
    {
        return $this->pdo->query("SELECT * FROM `note-app`")->fetchAll();
    }
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM `note-app` WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getTask(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `note-app` WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}