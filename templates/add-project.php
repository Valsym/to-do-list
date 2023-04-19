<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project): ?>
                <li class="main-navigation__list-item">
                    <a class="main-navigation__list-item-link"
                       href="/index.php?project_id=<?= $project['id'] ?>"><?= $project['project_name'] ?></a>
                    <span class="main-navigation__list-item-count"><?= list_items_count($tasks, $project) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form" action="add-project.php" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>
            <?php $classname = isset($errors['name']) || isset($errors['project']) ? "form__input--error" : ""; ?>
            <input class="form__input <?= $classname ?>" type="text" name="name" id="project_name"
                   value="<?= $project_name ?? "" ?>" placeholder="Введите название проекта">
            <span class="form__message"><?= $errors['name'] ?? "" ?></span>
            <span class="form__message"><?= $errors['project'] ?? "" ?></span>
        </div>

        <div class="form__row form__row--controls">
            <?php if (isset($errors) && count($errors)): ?>
                <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>