<?php
require_once("db.php");
require_once("helpers.php");
session_start();

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    $user_name = $_SESSION['user']['user_name'];

    $sql = 'select p.id, p.project_name, u.user_name as user_name from projects p ' .
        'join users u on u.id = user_id ' .
        'where user_id = ' . $user_id;

    $result = mysqli_query($con, $sql);
    if ($result) {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($con);
    }

    $sql = 'select task_status, task_name, deadline, p.project_name as category, project_id from tasks t ' .
        'join projects p on p.id = project_id ' .
        "where t.user_id = $user_id";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //print_r($all_tasks);
    } else {
        $error = mysqli_error($con);
        print("Ошибка2: $error");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $project = $_POST;
        $required_fields = ['name'];
        $errors = [];

        $project_name = $project['name'];

        $rules = [
            'name' => function ($project_name) use ($projects) {
                return validate_project_exist($project_name, $projects) ? null : "Проект уже существует";
            }
        ];


        foreach ($project as $key => $value) {
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
            $page_content = include_template("add-project.php", [
                'projects' => $projects,
                'tasks' => $tasks,
                'project_name' => $project_name,
                'errors' => $errors
            ]);
        } else {
            //Запись в БД и редирект на /

            $sql = "INSERT INTO `projects` ( `project_name`, `user_id`) VALUES" .
                "(?, ?)";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'si', $project_name, $user_id);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                header("Location: /index.php");
            } else {
                $error = mysqli_error($con);
                $title = "Ошибка БД: $error";
                $page_content = include_template("error.php", [
                    'error' => $error,
                    'projects' => $projects,
                    'tasks' => $tasks,
                    'user_name' => $user_name,
                    'title' => $title
                ]);
            }

        }

    } else {
        $page_content = include_template("add-project.php", [
            'projects' => $projects,
            'tasks' => $tasks,
        ]);
    }
} else {
    //$page_content = include_template("auth.php", []);
    header("Location: /index.php");
    exit;
}

$layout_content = include_template("layout-add.php", [
    'content' => $page_content,
    //'projects' => $projects,
    'user_name' => $user_name,
    'title' => 'Добавить проект'
]);

print($layout_content);
/*
print_r($fields);
echo "\n";
if (isset($error)) print_r($error);
*/
