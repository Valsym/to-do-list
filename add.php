<?php
require_once ("db.php");
require_once ("helpers.php");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}
else {
    //print("Соединение установлено\n");
}
$user_id = 1;

$sql = 'select p.id, p.project_name, u.user_name as user_name from projects p ' .
    'join users u on u.id = user_id ' .
    'where user_id = ' . $user_id;
//$sql = sprintf("select project_name from projects where user_id = %s", $user_id);
//print("\n".$sql."\n");


$result = mysqli_query($con, $sql);
if ($result) {
    $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    /*foreach($projects as $project) {
        print_r($project['project_name']);
    }*/
    //print_r($projects);
} else {
    $error = mysqli_error($con);
    //print("Ошибка1: $error");
}
//print($projects[0]['user_name']);
//exit;

//$params = $_GET;
//$project_id = $params['project_id'] ?? NULL;
$project_id = filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);

//print_r($project_id);
//exit;

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

$fields = filter_input_array(INPUT_POST, [
    'name' => FILTER_DEFAULT,
    'project' => FILTER_DEFAULT,
    'date' => FILTER_DEFAULT,
    'file' => FILTER_DEFAULT,
], true);

$page_content = include_template("main-add.php", [
    'projects' => $projects,
    'tasks' => $tasks,
    'field' => $fields,
    'errors' => []
]);

$page_404 = include_template("404.php", [
    'projects' => $projects,
    'tasks' => $tasks,
    'user' => $projects[0]['user_name'],
]);

$title = '';
/*
$page_content = include_template("main-add.php", [
    'projects' => $projects,
    'tasks' => $tasks,
    //'project_active' => $project_active,
]);
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required_fields = ['name', 'project', 'date'];
    $errors = [];

    $rules = [
        'date' => function($date) {
            return validate_data($date);
        },

        'project' => function($project) use ($projects) {
            return validate_project_exist($project, $projects);
        }
    ];

    $fields = filter_input_array(INPUT_POST, [
        'name' => FILTER_DEFAULT,
        'project' => FILTER_DEFAULT,
        'date' => FILTER_DEFAULT,
    ], true);

    foreach (/*$_POST*/ $fields as $key => $value) {
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

    if (!empty($_FILES['file']['name'])) {
        //print_r($_FILES['file']);
        $file_name = $_FILES['file']['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tmp_name = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];

        $file_type = finfo_file($finfo, $tmp_name);

        $ext = NULL;
        if ($file_type === 'image/gif') {
            $ext = 'gif';
        } elseif ($file_type === 'image/jpeg') {
            $ext = 'jpg';
        } elseif ($file_type === 'image/png') {
            $ext = 'png';
        }

        if ($file_size > 200000) {
            $errors['file_size'] = "Максимальный размер файла: 200Кб";
        }

        if ($ext && $file_size <= 200000) {
            $file_name = uniqid() . ".$ext";
            move_uploaded_file($tmp_name, $file_path . $file_name);
            $fields['path'] = $file_name;//"uploads/" . $file_name;
        } else {
            $errors['file_ext'] = "Допустимые форматы файла: gif, ipeg, png";
        }



    }


    if (count($errors)) {
        // показать ошибку валидации
        $page_content = include_template("main-add.php", [
            'projects' => $projects,
            'tasks' => $tasks,
            'field' => $fields,
            'errors' => $errors
        ]);
    } else {
        //Запись в БД и редирект на Главную
        $sql = "INSERT INTO `tasks` (`task_dt_add`, `task_status`, `task_name`, `task_file`, `deadline`,".
            " `user_id`, `project_id`) VALUES".
            "(now(), 0, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);

        $task_name = $fields['name'];
        $task_file = $fields['path'] ?? "";
        $deadline = $fields['date'];
        $project_id = $fields['project'];
        //$user_id = $projects[$project_id]['user_id'];
        mysqli_stmt_bind_param($stmt, 'sssii', $task_name, $task_file, $deadline, $user_id, $project_id);
        $res = mysqli_stmt_execute($stmt);

        if($res) {
            header("Location: /index.php");
        } else {
            $error = mysqli_error($con);
            $title = "Ошибка БД: $error";
            $page_content = include_template("error.php", [
                'error' => $error,
                'projects' => $projects,
                'tasks' => $tasks,
                'user_name' => $projects[0]['user_name'],
                'title' => $title
                ]);
        }

    }

}

if (!$title)
    $title = 'Добавить задачу';

$layout_content  = include_template("layout-add.php", [
    'content' => $page_content,
    //'projects' => $projects,
    'user_name' => $projects[0]['user_name'],
    'title' => $title
]);

print($layout_content );
/*
print_r($fields);
echo "\n";
if (isset($error)) print_r($error);
*/
?>