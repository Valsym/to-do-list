<?php
require_once ("db.php");
require_once ("helpers.php");
session_start();
if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}
else {
    //print("Соединение установлено\n");
}

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];
    //print_r($_SESSION['user']);

//echo "redirect2";
    //exit;
/*}else {
    echo "not SESSION";
    exit;
}*/

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
/*
//$params['project_id'] = $params['project_id'] ?? $project_id;
$scriptname = pathinfo(__FILE__, PATHINFO_BASENAME);
$query = http_build_query($params);
$url = "/" . $scriptname . "?" . $query;
*/
$sql = 'select task_status, task_name, task_file, deadline, p.project_name as category, project_id from tasks t ' .
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
/*
$page_404 = include_template("404.php", [
    'projects' => $projects,
    'tasks' => $tasks,
    'user' => $projects[0]['user_name'],
]);*/

//$tasks = NULL;
$project_active = NULL;
if ($project_id) {
    $project_find = false;
    foreach ($projects as $project) {
        if (in_array($project_id, $project)) {
            //$sql .= " and p.id = $project_id";
            $project_find = true;
            /*$result = mysqli_query($con, $sql);
            if ($result) {
                $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

                //print_r($tasks);
            } else {
                $error = mysqli_error($con);
                print("Ошибка3: $error");
            }*/
            break;
        }
    }
    if (!$project_find) {
        //header("Location: /404.php");
        $error = "Ошибка 404: проект с таким id=$project_id не существует";
        $page_content = include_template("error.php", [
            'error' => $error,
            'projects' => $projects,
            'tasks' => $tasks,
            'user_name' => $projects[0]['user_name'],
            'title' => 'Ошибка 404: страница не существует'
        ]);
        print($page_content);
        die;
        //print($page_404);
        //die;
    } else {
        $project_active = $project_id;
    }
}
//print("\n".$sql."\n");

//print($projects[0]['user_name']);
//exit;

$show_complete_tasks = rand(0, 1);
/*
if ($tasks === NULL) {
    $tasks = $all_tasks;
}*/
//if ($project_find) {
//if (isset($_SESSION)) {
    $page_content = include_template("main.php", [
        'projects' => $projects,
        'tasks' => $tasks,
        //'all_tasks' => $all_tasks,
        'project_active' => $project_active,
        'show_complete_tasks' => $show_complete_tasks
    ]);
} else {
    $page_content = include_template("guest.php", []);
}

$layout_content  = include_template("layout.php", [
    'content' => $page_content,
    //'user_name' => $projects[0]['user_name'],
    'title' => 'Дела в порядке'
]);

print($layout_content );

//if (isset($_SESSION)) print_r($_SESSION);

?>
