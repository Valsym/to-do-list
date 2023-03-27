<?php
require_once ("db.php");
require_once ("helpers.php");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}
else {
    //print("Соединение установлено\n");
}
$user_id = 2;

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

$sql = 'select task_status, task_name, deadline, p.project_name as category, project_id from tasks t ' .
    'join projects p on p.id = project_id ' .
    "where t.user_id = $user_id";
$result = mysqli_query($con, $sql);
if ($result) {
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    //print_r($tasks);
} else {
    $error = mysqli_error($con);
    print("Ошибка2: $error");
}
//print($projects[0]['user_name']);
//exit;

$show_complete_tasks = rand(0, 1);
$page_content = include_template("main.php", [
        'projects' => $projects,
        'tasks' => $tasks,
        'show_complete_tasks' => $show_complete_tasks
]);

$layout_content  = include_template("layout.php", [
    'content' => $page_content,
    'user' => $projects[0]['user_name'],
    'title' => 'Дела в порядке'
]);

print($layout_content );



?>
