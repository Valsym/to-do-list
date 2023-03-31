<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Рассчитывает оставшееся время до определенной даты
 * @param string $deadline дата окончания выполнения задачи
 * @return true если осталось меньше 24 часов или false  в противном случае
 */
function get_time_left($deadline) {
    $endTime = strtotime($deadline);
    $nowTime = strtotime("today");//time();

    $diff = floor(($endTime - $nowTime) / 3600);

    //file_put_contents("log.txt", "\ndiff=$diff", FILE_APPEND); // отладочная печать

    return $diff;
}

/**
 * Проверка даты
 * Содержимое поля «дата завершения» должно быть датой в формате «ГГГГ-ММ-ДД»;
 * Эта дата должна быть больше или равна текущей.
 * @param $date
 * @return string
 */

function validate_data($date) {
    if (!is_date_valid($date)) {
        return "Дата должна быть в формате «ГГГГ-ММ-ДД»";
    };

    if (get_time_left($date) < 0) {
        return "Дата должна быть больше или равна текущей";
    };
}

/**
 * Рассчитывает кол-во задач в заданной категории проекта
 * @param array $tasks массив задач
 * @param string $project категория проекта
 * @return $sum кол-во задач в заданной категории проекта
 */
function list_item_сount(array $tasks, $project) {
    $sum = 0;
    foreach($tasks as $task) {
        if ($task['project_id'] === $project['id'])
            $sum++;
    }
    return $sum;
}
/*
function get_project_id_url($project_id) {
    $params = $_GET;
    $params['project_id'] = $params['project_id'] ?? $project_id;

    $scriptname = pathinfo(__FILE__, PATHINFO_BASENAME);
    $query = http_build_query($params);
    $url = "/" . $scriptname . "?" . $query;
    $url = "/index.php?" . $query;
    $url = "/index.php?project_id=" . $project_id;

    return $url;
}*/


/**
 * Проверка заполненности
 * @param string $name имя поля для проверки
 * @return строка ошибки
 */
function validate_filled($name) {
    if (empty($_POST[$name])) {
        return "Это поле должно быть заполнено";
    }
}

/**
 * Проверка существования проекта
 * @param string $name имя поля для проверки
 * @return строка ошибки
 */
function validate_project_exist($project_name, $projects) {
    foreach ($projects as $project) {
        if (in_array($project_name, $project)) {
            return NULL;
        }
    }
    return "Указан несуществующий проект";
}

/**
 * Проверка длины
 * @param string $name имя поля для проверки
 * @return строка ошибки
 */
function is_correct_length($name, $min, $max) {
    //$len = strlen($_POST[$name]);
    $len = strlen($name);
    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }
}

function getPostVal($name) {
    return filter_input(INPUT_POST, $name);
}

function validate_email(/*$email*/) {
    /*print($email);
    var_dump(filter_var($email, FILTER_VALIDATE_EMAIL));
    var_dump(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
    die;*/
    if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL))
        return "E-mail введён некорректно";
}

function validate_name($name, $min, $max) {
    //$len = strlen($_POST[$name]);
    $len = mb_strlen($name);
    //echo "$name, $len";die;
    if ($len < $min or $len > $max) {
        return "Имя должно быть от $min до $max символов";
    }
}

function check_duplicate($con, $row, $value) {
    $sql = "select $row from users where $row = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        $errorMsg = 'check_duplication(): Не удалось инициализировать подготовленное выражение: ' . mysqli_error($con);
        die($errorMsg);
    }

    mysqli_stmt_bind_param($stmt, 's', $value);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res);
}

