<?php
function listItemCount(array $tasks, $project) {
    $sum = 0;
    foreach($tasks as $task) {
        if ($task['category'] === $project)
            $sum++;
    }
    return $sum;
}