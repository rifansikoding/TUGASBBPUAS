<?php

class TodoManager
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addTodo($title)
    {
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

    public function getTodos()
    {
        return $this->conn->query("SELECT * FROM todos ORDER BY id DESC");
    }
}

require 'db_conn.php';

$todoManager = new TodoManager($conn);

if (isset($_POST['title'])) {
    $title = $_POST['title'];
    $result = $todoManager->addTodo($title);

    if ($result === 'success') {
        header("Location: index.php?mess=success");
        exit();
    } else {
        header("Location: index.php?mess=error");
        exit();
    }
}

$todos = $todoManager->getTodos();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Catatan Penting</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="main-section">
        <div class="add-section">
            <form action="index.php" method="POST" autocomplete="off">
                <?php if (isset($_GET['mess']) && $_GET['mess'] == 'error') { ?>
                    <input type="text" name="title" style="border-color: #ff6666" placeholder="Tulis Catatan" />
                    <button type="submit">Add &nbsp; <span>&#43;</span></button>
                <?php } else { ?>
                    <input type="text" name="title" placeholder="Tulis Catatan Anda Disini  !!" />
                    <button type="submit">Tambah &nbsp; <span>&#43;</span></button>
                <?php } ?>
            </form>
        </div>

        <div class="show-todo-section">
            <?php if ($todos->rowCount() <= 0) { ?>
                <div class="todo-item">
                    <div class="empty">
                        <img src="img/online-marketing-2037545_1280.png" width="100%" />
                    </div>
                <?php } ?>

                <?php while ($todo = $todos->fetch(PDO::FETCH_ASSOC)) { ?>
                    <div class="todo-item">
                        <span id="<?php echo $todo['id']; ?>" class="remove-to-do">x</span>
                        <?php if ($todo['checked']) { ?>
                            <input type="checkbox" class="check-box" data-todo-id="<?php echo $todo['id']; ?>" checked />
                            <h2 class="checked"><?php echo $todo['title'] ?></h2>
                        <?php } else { ?>
                            <input type="checkbox" data-todo-id="<?php echo $todo['id']; ?>" class="check-box" />
                            <h2><?php echo $todo['title'] ?></h2>
                        <?php } ?>
                        <br>
                        <small>Dibuat: <?php echo $todo['date_time'] ?></small>
                    </div>
                <?php } ?>
        </div>
    </div>

    <script src="js/jaquery.js"></script>

    <script>
        $(document).ready(function () {
            $('.remove-to-do').click(function () {
                const id = $(this).attr('id');

                $.post("app/remove.php", {
                        id: id
                    },
                    (data) => {
                        if (data) {
                            $(this).parent().hide(600);
                        }
                    }
                );
            });

            $(".check-box").click(function (e) {
                const id = $(this).attr('data-todo-id');

                $.post('app/check.php', {
                        id: id
                    },
                    (data) => {
                        if (data != 'error') {
                            const h2 = $(this).next();
                            if (data === '1') {
                                h2.removeClass('checked');
                            } else {
                                h2.addClass('checked');
                            }
                        }
                    }
                );
            });
        });
    </script>
</body>

</html>
