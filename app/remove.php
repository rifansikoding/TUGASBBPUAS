<?php

class TodoManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function deleteTodo($id) {
        if (empty($id)) {
            return 0;
        }

        $stmt = $this->conn->prepare("DELETE FROM todos WHERE id=?");
        $res = $stmt->execute([$id]);

        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }
}

if (isset($_POST['id'])) {
    require '../db_conn.php';

    $id = $_POST['id'];

    $todoManager = new TodoManager($conn);
    $result = $todoManager->deleteTodo($id);

    echo $result;
} else {
    header("Location: ../index.php?mess=error");
}
