<!--<header class="main-header">
    <a href="#">
        <img src="../img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
    </a>

    <div class="main-header__side">
        <a class="main-header__side-item button button--transparent" href="/auth.phphtml">Войти</a>
    </div>
</header>-->

<div class="content">

    <section class="content__side">
        <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

        <a class="button button--transparent content__side-button" href="/auth.php">Войти</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Вход на сайт</h2>

        <form class="form" action="/auth.php" method="post" autocomplete="off">
            <div class="form__row">
                <?php $classname = isset($errors['email']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="email">E-mail <sup>*</sup></label>
                <?php $value = isset($user['email']) ? $user['email'] : ""; ?>
                <input class="form__input <?= $classname ?>" type="text" name="email" id="email"
                       value="<?= $value ?>" placeholder="Введите e-mail">
                <span class="form__message"><?= $errors['email'] ?? "" ?></span>
                <!--<p class="form__message">E-mail введён некорректно</p>-->
            </div>

            <div class="form__row">
                <?php $classname = isset($errors['password']) ? "form__input--error" : ""; ?>
                <label class="form__label" for="password">Пароль <sup>*</sup></label>
                <?php $value = isset($user['password']) ? $user['password'] : ""; ?>
                <input class="form__input <?= $classname ?>" type="password" name="password" id="password"
                       value="<?= $value ?>" placeholder="Введите пароль">
                <span class="form__message"><?= $errors['password'] ?? "" ?></span>
            </div>

            <div class="form__row form__row--controls">
                <?php if (isset($errors) && count($errors)): ?>
                    <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
                <?php endif; ?>
                <?php $errorDB = $errorDB ?? ""; ?>
                <p class="error-message"><?= $errorDB ?></p>
                <input class="button" type="submit" name="" value="Войти">
            </div>
        </form>

    </main>

</div>