<?php
require_once ("db.php");
require_once ("helpers.php");
session_start();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST;
    $required_fields = ['email', 'password'];
    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
            //echo "\nerrors[$field] => $errors[$field]\n";
        }
    }

    $errors = array_filter($errors);

    if (count($errors)) {
        // показать ошибку валидации
        $page_content = include_template("auth.php", [
            'errors' => $errors,
        ]);
    } else {
        $email = mysqli_real_escape_string($con, $user['email']);
        $sql = "select * from users where email = '$email'"; // !!! '$email' - без таких кавычек не работает
        $res = mysqli_query($con, $sql);
        $user_real = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : NULL;

        if (!$user_real) {
            //echo "$email\n$sql";exit;
            $errors['email'] = 'Вы ввели неверный email';
        } else {
            if (password_verify($user['password'], $user_real['user_pass'])) {
                //session_start();
                $_SESSION['user'] = $user_real;
                //print_r($_SESSION['user']);
                //echo "redirect1";//exit;
                header("Location: /index.php");
                exit();
            } else {
                $errors['password'] = 'Вы ввели неверный пароль';
                //echo "user_pass=".$user_real['user_pass'];exit;
            }

        }
        if (count($errors)) {
            // показать ошибку валидации
            $page_content = include_template("auth.php", [
                'user' => $user,
                'errors' => $errors,
            ]);
        }
    }
} else {
    $page_content = include_template("auth.php", []);
    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
    $page_content = include_template("auth.php", []);

}

$layout_content = include_template('layout.php', [
    'content'    => $page_content,
    'categories' => [],
    'title'      => 'Дела в порядке'
]);

print($layout_content);