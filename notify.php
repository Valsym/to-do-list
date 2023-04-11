<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once 'vendor/autoload.php';
require_once("db.php");
require_once("helpers.php");
require_once("config/config.php");

// https://github.com/symfony/mailer
// Конфигурация траспорта
$dsn = "smtp://$log:$pass@smtp.yandex.ru:465?encryption=SSL";

/* Так не работает:
// Конфигурация траспорта
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);
// Формирование сообщения
$message = new Email();
$message->to("$mail");
$message->from("$mailfrom");
$message->subject("Просмотры вашей гифки");
$message->text("Вашу гифку «Кот и пылесос» посмотрело больше 1 млн!");
// Отправка сообщения
$mailer->send($message);
*/

/*
// Отправка через yandex.ru - работает!!!
$mailfrom = $mail;
$dsn = "smtp://$log:$pass@smtp.yandex.ru:465?encryption=SSL";
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);
// Формирование сообщения
$email = (new Email())
    ->to("$mail")
    ->from("$mailfrom")
    ->subject("Уведомление от сервиса «Дела в порядке»")
    ->text("Уважаемый, %имя пользователя%. У вас запланирована задача %имя задачи% на %время задачи%");
// Отправка сообщения
//$mailer = new Mailer($email);
$mailer->send($email);
*/

/* Отправка через GMAIL.COM - работает
$loggmail = "login";
$passgmail = "supersecretpass";
$mailfrom = '$loggmail@gmail.com";
$dsn = 'smtp://$loggmail:$passgmail@smtp.gmail.com:465?encryption=SSL';
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);
// Формирование сообщения
$email = (new Email())
    ->to("$mail")
    ->from("$mailfrom")
    ->subject("Просмотры вашей гифки")
    ->text("Вашу гифку «Кот и пылесос» посмотрело больше 1 млн!");
// Отправка сообщения
//$mailer = new Mailer($email);
$mailer->send($email);
*/

/* Не работает на локалке
$address = $mail;
$sub = "Todolist test";
$mes = "Вашу гифку «Кот и пылесос» посмотрело больше 1 млн!\n";
$send = mail ($address,$sub,$mes,"Content-type:text/plain; charset = UTF-8\r\nFrom:$address");
print_r($send);
*/

$sql = "select id, user_name, email from users";
$res1 = mysqli_query($con, $sql);
$notes = [];
if ($res1) {
    $users = mysqli_fetch_all($res1, MYSQLI_ASSOC);
    foreach ($users as $user) {
        $user_id = $user['id'];
        //echo "\nuser_id=$user_id  ".$user['user_name']."  ";
        $sql = 'select task_name, deadline, user_name, u.id as uid from tasks t ' .
            'join users u on t.user_id = u.id ' .
            "where task_status = '0' and u.id = $user_id";
        $res2 = mysqli_query($con, $sql);
        if ($res2) {
            $tasks = mysqli_fetch_all($res2, MYSQLI_ASSOC);
            foreach ($tasks as $task) {
                $deadline = $task['deadline'];
                $diff = get_time_left($deadline . "+1 day");
                //echo $task['task_name']." diff=$diff \n";
                if ($diff >= 0 && $diff <= 24) {
                    $notes[] = [//$user_id => [
                        'user_name' => $user['user_name'],
                        'task_name' => $task['task_name'],
                        'deadline' => $deadline,
                        'email' => $user['email']
                        //]
                    ];
                }
            }
        } else {
            $error = mysqli_error($con);
            print("Ошибка2: $error");
        }
    }
} else {
    $error = mysqli_error($con);
    print("Ошибка1: $error");
}

//print_r($notes);
$mess = [];
foreach ($notes as $k => $v) {
    $date = date_format(date_create($v['deadline']), 'd-m-Y');
    if ($k > 0 && $v['user_name'] === $notes[$k - 1]['user_name']) {
        $mess[$v['user_name']] .= "А также задача «" . $v['task_name'] . "» на $date.\n";
        $email[$v['user_name']] = $notes[$k - 1]['email'];
    } else {
        $mess[$v['user_name']] = "Уважаемый, " . $v['user_name'] . ".\nУ вас запланирована задача «" .
            $v['task_name'] . "» на $date.\n";
        $emailto[$v['user_name']] = $notes[$k]['email'];
    }
}


//$mailfrom = 'keks@phpdemo.ru';
$dsn = "smtp://$log:$pass@smtp.yandex.ru:465?encryption=SSL";
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);

foreach ($mess as $k => $mes) {
    $mailto = $emailto[$k];
    //echo "\n$k -> $mailto -> $mes";
    // Формирование сообщения
    $email = (new Email())
        ->to("$mailto")
        ->from("$mailfrom")
        ->subject("Уведомление от сервиса «Дела в порядке»")
        ->text("$mes");
    // Отправка сообщения
    $mailer->send($email);
}

