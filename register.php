<?php
require_once("db.php");
require_once("helpers.php");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
    exit();
    //$error = "Ошибка подключения: " . mysqli_connect_error());
} else {
    //print("Соединение установлено\n");
}

$title = 'Регистрация';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST;
    $required_fields = ['email', 'password', 'name'];
    $errors = [];

    $rules = [
        'email' => function () {
            return validate_email();
        },

        'password' => function ($password) {
            return is_correct_length($password, 6, 8);
        },

        'name' => function ($name) {
            return validate_name($name, 3, 12);
        }
    ];

    foreach ($user as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    $errors = array_filter($errors);

    if (count($errors)) {
        // показать ошибку валидации
        $page_content = include_template("reg.php", [
            'errors' => $errors,
            'errorDB' => $errorDB ?? ''
        ]);
    } else {
        // проверка на дубли email && name
        if (check_user($con, 'email', $user['email'])) {
            $errors['email'] = 'Пользователь с таким E-mail уже зарегистрирован';
        }

        if (check_user($con, 'user_name', $user['name'])) {
            $errors['name'] = 'Пользователь с таким именем уже зарегистрирован';
        }

        //die;
        if (count($errors)) {
            // показать ошибку валидации
            $page_content = include_template("reg.php", [
                'errors' => $errors,
                'errorDB' => $errorDB ?? ''
            ]);
        } else {

            //Запись в БД и редирект на login.php
            $sql = 'INSERT INTO users (user_dt_add, email, user_pass, user_name) VALUES (NOW(), ?, ?, ?)';

            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
            $stmt = db_get_prepare_stmt($con, $sql, $user);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                $user_id = mysqli_insert_id($con);
                header("Location: index.php");
                exit();
            } else {
                $errorDB = "Ошибка БД при добавлении нового юзера: " . mysqli_error($con);
            }
        }
    }

}

$page_content = include_template("reg.php", [
    'errors' => $errors,
    'errorDB' => $errorDB ?? ''
]);


$layout_content = include_template("layout.php", [
    'content' => $page_content,
    'title' => 'Дела в порядке | Регистрация'
]);
