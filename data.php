<?php
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'task' => 'Собеседование в IT компании',
        'deadline' => '20.03.2023',
        'category' => 'Работа',
        'completed' => 'false'
    ],
    [
        'task' => 'Выполнить тестовое задание',
        'deadline' => '25.12.2023',
        'category' => 'Работа',
        'completed' => false
    ],
    [
        'task' => 'Сделать задание первого раздела',
        'deadline' => '21.12.2023',
        'category' => 'Учеба',
        'completed' => true
    ],
    [
        'task' => 'Встреча с другом',
        'deadline' => '22.12.2023',
        'category' => 'Входящие',
        'completed' => false
    ],
    [
        'task' => 'Купить корм для кота',
        'deadline' => null,
        'category' => 'Домашние дела',
        'completed' => false
    ],
    [
        'task' => 'Заказать пиццу',
        'deadline' => null,
        'category' => 'Домашние дела',
        'completed' => false
    ],
];
