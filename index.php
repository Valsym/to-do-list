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
        //print("Ошибка1: $error");
    }

    $project_id = filter_input(INPUT_GET, 'project_id', FILTER_SANITIZE_NUMBER_INT);
    $task_id = filter_input(INPUT_GET, 'task_id', FILTER_SANITIZE_NUMBER_INT);
    $check = filter_input(INPUT_GET, 'check', FILTER_SANITIZE_NUMBER_INT);
    $show_complete_tasks = filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_NUMBER_INT);
    $today = filter_input(INPUT_GET, 'today', FILTER_SANITIZE_NUMBER_INT);
    $tomorrow = filter_input(INPUT_GET, 'tomorrow', FILTER_SANITIZE_NUMBER_INT);
    $expired = filter_input(INPUT_GET, 'expired', FILTER_SANITIZE_NUMBER_INT);


    $sql = 'select t.id, task_status, task_name, task_file, deadline, p.project_name as category, project_id from tasks t ' .
        'join projects p on p.id = project_id ' .
        "where t.user_id = $user_id";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $tasks_all = $tasks;
    } else {
        $error = mysqli_error($con);
        print("Ошибка2: $error");
    }

    if (isset($today) && $today) {
        $tasks = array_filter($tasks, function ($task) {
            $diff = get_time_left($task['deadline']);
            return ($diff < 24 && $diff >= 0);
        });
    }


    if (isset($expired) && $expired) {
        $tasks = array_filter($tasks, function ($task) {
            $diff = get_time_left($task['deadline']);
            return ($diff < 0 && !$task['task_status']);
        });
    }

    if (isset($tomorrow) && $tomorrow) {
        $tasks = array_filter($tasks, function ($task) {
            $diff = get_time_left($task['deadline']);
            return ($diff < 48 && $diff >= 24);
        });
    }

    if (isset($show_complete_tasks) && !$show_complete_tasks) {
        $tasks = array_filter($tasks, function ($task) {
            return !$task['task_status'];
        });
    }

    /*
    $page_404 = include_template("404.php", [
        'projects' => $projects,
        'tasks' => $tasks,
        'user' => $projects[0]['user_name'],
    ]);*/

    $project_active = null;
    if ($project_id) {
        $project_find = false;
        foreach ($projects as $project) {
            if (in_array($project_id, $project)) {
                $project_find = true;
                break;
            }
        }
        if (!$project_find) {
            $error = "Ошибка 404: проект с таким id=$project_id не существует";
            $page_content = include_template("error.php", [
                'error' => $error,
                'projects' => $projects,
                'tasks' => $tasks,
                'user_name' => $user_name,
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

    $task_active = null;
    if ($task_id) {
        $task_find = false;
        foreach ($tasks as $task) {
            if (in_array($task_id, $task)) {
                $task_find = true;
                break;
            }
        }
        if (!$task_find && $check) {
            $error = "Ошибка 404: задача с таким id=$task_id не существует";
            $page_content = include_template("error.php", [
                'error' => $error,
                'projects' => $projects,
                'tasks' => $tasks,
                'user_name' => $user_name,
                'title' => 'Ошибка 404: страница не существует'
            ]);
            print($page_content);
            die;
            //print($page_404);
            //die;
        } else {
            $task_active = $task_id;
            $sql = "SELECT task_status FROM tasks WHERE id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $task_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $task_status = mysqli_fetch_assoc($res);
            $task_status = $task_status['task_status'];
            //echo "$sql <-- $task_status, $task_id       check=$check\n";

            $task_status = $task_status ? 0 : 1;
            $sql = "update tasks set task_status = ? where id = ?";
            $stmt = mysqli_prepare($con, $sql);
            if ($stmt === false) {
                $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($con);
                die($errorMsg);
            }
            mysqli_stmt_bind_param($stmt, 'ii', $task_status, $task_id);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                $check = 0;
                $_GET['check'] = 0;
                $project_param = isset($project_id) ? 'project_id=' . $project_id : '';
                header("Location: index.php?$project_param");
                exit();
            } else {
                $errorDB = "Ошибка БД при добавлении нового юзера: " . mysqli_error($con);
                echo "\nОшибка БД: " . mysqli_error($con);
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search'])) {
        $search = trim(htmlspecialchars($_GET['search']));
        if ($search) {
            $sql = "select task_name from tasks where match(task_name) against( ? )";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 's', $search);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
            $search_tasks = [];
            if (count($rows)) {
                foreach ($rows as $k => $row) {
                    $search_tasks[] = $row['task_name'];
                }
            }
        }
    }

    $page_content = include_template("main.php", [
        'projects' => $projects,
        'tasks' => $tasks,
        'tasks_all' => $tasks_all,
        'search' => $search ?? "",
        'search_tasks' => $search_tasks ?? [],
        'project_active' => $project_active,
        'show_complete_tasks' => $show_complete_tasks
    ]);

} else {
    $page_content = include_template("guest.php", []);
}

$layout_content = include_template("layout.php", [
    'content' => $page_content,
    'title' => 'Дела в порядке'
]);

print($layout_content);


