<?php
require_once ("data.php");
require_once ("functions.php");
require_once ("helpers.php");

$content = include_template("main.php", [
        'projects' => $projects,
        'tasks' => $tasks,
        'show_complete_tasks' => $show_complete_tasks
]);

layout_content  = include_template("layout.php", [
    'content' => $content,
    'user' => 'Константин',
    'title' => 'Дела в порядке'
]);

print(layout_content );

?>
