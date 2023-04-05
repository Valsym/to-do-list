<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach($projects as $project): ?>
                <li class="main-navigation__list-item <?php if($project_active === $project['id']):
                    ?>main-navigation__list-item--active<?php endif ?>">
                    <a class="main-navigation__list-item-link" href="/index.php?project_id=<?=$project['id'] ?>"><?=$project['project_name'] ?></a>
                    <span class="main-navigation__list-item-count"><?=list_item_сount($tasks, $project) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
       href="/add-project.php" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?=$search ?? "" ?>" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>
    <?php if (isset($search) && $search) {
    if($search_tasks = $search_tasks ?? "") { ?>
        <h2>Результаты поиска по запросу "<span><?=$search ?></span>"</h2>
        <?php
        foreach($search_tasks as $task) { ?>
            <p><?=htmlspecialchars($task) ?></p>
        <?php }
        } else { ?>
            <h2>Ничего не найдено по вашему запросу</h2>
    <?php }
    } ?>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item <?php
            if(empty($_GET) || (!isset($_GET['today']) && !isset($_GET['tomorrow']) && !isset($_GET['expired']))):
                ?> tasks-switch__item--active<?php endif; ?>">Все задачи</a>
            <a href="/index.php?today=1" class="tasks-switch__item<?php
            if(isset($_GET['today']) && $_GET['today']): ?> tasks-switch__item--active<?php endif; ?>">Повестка дня</a>
            <a href="/?tomorrow=1" class="tasks-switch__item<?php
            if(isset($_GET['tomorrow']) && $_GET['tomorrow']): ?> tasks-switch__item--active<?php endif; ?>">Завтра</a>
            <a href="/?expired=1" class="tasks-switch__item<?php
            if(isset($_GET['expired']) && $_GET['expired']): ?> tasks-switch__item--active<?php endif; ?>">Просроченные</a>
        </nav>

        <label class="checkbox">
            <!--добавить сюда атрибут "checked", если переменная $show_complete_tasks равна единице-->
            <input class="checkbox__input visually-hidden show_completed" type="checkbox"<?php
            if(($show_complete_tasks == 1 || $show_complete_tasks == '')
                && !isset($_GET['expired'])): ?> checked<?php endif; ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">
        <?php foreach($tasks as $task) {
            if ($show_complete_tasks === 0 && $task['task_status'] === true ||
                ($project_active && $project_active != $task['project_id']))
                continue;
            else { ?>
                <tr class="tasks__item task<?php if($task['task_status'] > 0) { ?> task--completed<?php }; ?>
                    <?php if(get_time_left($task['deadline']) < 24):?> task--important<?php endif ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" value="<?=$task['id'] ?>" type="checkbox">
                            <span class="checkbox__text"><?=htmlspecialchars($task['task_name']) ?></span>
                        </label>
                    </td>

                    <td class="task__file">
                        <?php if($task['task_file']): ?>
                            <a href="/uploads/<?=$task['task_file'] ?>"><?=$task['task_file'] ?></a>
                        <?php endif; ?>
                    </td>

                    <td class="task__date"><?=$task['deadline'] ?></td>

                    <td class="task__controls">
                    </td>
                </tr>
            <?php }
        } ?>
    </table>
</main>