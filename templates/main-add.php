<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach($projects as $project): ?>
                <li class="main-navigation__list-item">
                    <a class="main-navigation__list-item-link"
                       href="/index.php?project_id=<?=$project['id'] ?>"><?=$project['project_name'] ?></a>
                    <span class="main-navigation__list-item-count"><?=list_item_сount($tasks, $project) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление задачи</h2>

    <?php $classname = (isset($errors) && count($errors) > 0) ? "form--invalid" : ""; ?>
    <form class="form <?=$classname ?>"  action="add.php" method="post" enctype="multipart/form-data" autocomplete="off">

        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>
            <?php $classname = isset($errors['name']) ? "form__input--error" : ""; ?>
            <input class="form__input  <?=$classname ?>" type="text" name="name" id="name"
                   value="<?=getPostVal('name') ?>" placeholder="Введите название">
        <span class="error-message"><?=$errors['name'] ?? "" ?></span>
        </div>


        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>
            <?php $classname = isset($errors['project']) ? "form__input--error" : ""; ?>
            <select class="form__input form__input--select <?=$classname ?>" name="project" id="project">
                <?php foreach($projects as $project): ?>
                <option value="<?=$project['id'] ?>" <?php if($project['id'] ===
                    getPostVal('project')): ?>  selected<?php endif; ?>><?=$project['project_name'] ?></option>
                <?php endforeach; ?>
            </select>
            <span class="error-message"><?=$errors['project'] ?? "" ?></span>
        </div>


        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>
            <?php $classname = isset($errors['date']) ? "form__input--error" : ""; ?>
            <input class="form__input form__input--date <?=$classname ?>" type="text" name="date" id="date"
                   value="<?=getPostVal('date') ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <span class="error-message"><?=$errors['date'] ?? "" ?></span>
        </div>

        <div class="form__row">
            <label class="form__label" for="file">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="file" id="file" value="<?=$field['path'] ?? ""; ?>">
                <span class="error-message"><?=$errors['file_ext'] ?? "" ?></span>
                <span class="error-message"><?=$errors['file_size'] ?? "" ?></span>
                <label class="button button--transparent" for="file">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>