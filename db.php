<?php

$con = mysqli_connect("localhost", "root", "", "todolist");
mysqli_set_charset($con, "utf8");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
    exit();
}


