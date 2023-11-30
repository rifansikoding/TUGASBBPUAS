<?php

class TodoManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function toggleTodo($id) {
        try {
            $todos = $this->conn->prepare("SELECT id, checked FROM todos WHERE id=?");
            $todos->execute([$id]);

            $todo = $todos->fetch();
            $uId = $todo['id'];
            $checked = $todo['checked'];

            $uChecked = $checked ? 0 : 1;

            $res = $this->conn->query("UPDATE todos SET checked=$uChecked WHERE id=$uId");

            if ($res) {
                return $checked;
            } else {
                return 'error';
            }
        } catch (PDOException $e) {
            return 'error';
        }
    }
}

require '../db_conn.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    if (empty($id)) {
        echo 'error';
    } else {
        $todoManager = new TodoManager($conn);
        echo $todoManager->toggleTodo($id);
    }
} else {
    header("Location: ../index.php?mess=error");
}
