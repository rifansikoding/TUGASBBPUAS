<?php

class TodoManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addTodo($title) {
        if (empty($title)) {
            return 'error';
        }

        try {
            $stmt = $this->conn->prepare("INSERT INTO todos(title) VALUE(?)");
            $res = $stmt->execute([$title]);

            if ($res) {
                return 'success';
            } else {
                return 'error';
            }
        } catch (PDOException $e) {
            return 'error';
        }
    }
}

require '../db_conn.php';

if (isset($_POST['title'])) {
    $title = $_POST['title'];

    $todoManager = new TodoManager($conn);
    $result = $todoManager->addTodo($title);

    if ($result === 'success') {
        header("Location: ../index.php?mess=success");
    } else {
        header("Location: ../index.php?mess=error");
    }
} else {
    header("Location: ../index.php?mess=error");
}