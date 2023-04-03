<?php
require_once ("db.php");
require_once ("helpers.php");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
    exit();
    //$error = "Ошибка подключения: " . mysqli_connect_error());
}
else {
    //print("Соединение установлено\n");
}

$title = 'Регистрация';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST;
    $required_fields = ['email', 'password', 'name'];
    $errors = [];

    $rules = [
        'email' => function (/*$email*/) {
            return validate_email(/*$email*/);
        },

        'password' => function ($password) {
            return is_correct_length($password, 6, 8);
        },

        'name' => function ($name) {
            return validate_name($name, 3, 12);
    }
    ];
/*
    $user = filter_input_array(INPUT_POST, [
        'email' => FILTER_DEFAULT,
        'password' => FILTER_DEFAULT,
        'name' => FILTER_DEFAULT,
    ], true);
    $user = $_POST;*/
    foreach (/*$_POST*/ $user as $key => $value) {
        //echo "\n$key => $value\n";
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
            //echo "\nerrors[$key] => $errors[$key]\n";
        }
    }

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
            //echo "\nerrors[$field] => $errors[$field]\n";
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
            //echo "$sql\n";


            if ($res) {
                $user_id = mysqli_insert_id($con);
                //echo "\nДобавлен новый юзер: $user_id\n";print_r($user);exit;
                header("Location: index.php");//?id=" . $user_id);
                exit();
            } else {
                //$content = include_template('error.php', ['error' => mysqli_error($con)]);
                $errorDB = "Ошибка БД при добавлении нового юзера: ".mysqli_error($con);
                //echo "\nОшибка БД при добавлении нового юзера: ".mysqli_error($con);print_r($user);exit;
            }
        }
    }

}

/*
$error = mysqli_error($con);
$title = "Ошибка БД: $error";
$page_content = include_template("error.php", [
    'error' => $error,
    'projects' => $projects,
    'tasks' => $tasks,
    'user_name' => $projects[0]['user_name'],
    'title' => $title
]);
*/

//if (!$title)

/*
$layout_content  = include_template("reg.php", [

    'title' => $title,
    'errors' => $errors,
    'errorDB' => $errorDB ?? ''
]);

print($layout_content );
*/
$page_content = include_template("reg.php", [
    'errors' => $errors,
    'errorDB' => $errorDB ?? ''
]);
//}

$layout_content  = include_template("layout.php", [
    'content' => $page_content,
    'title' => 'Дела в порядке | Регистрация'
]);

print($layout_content );