<div class="form">
    <h1>Регистрация</h1>
    <div class="hidden" id="activation">
        Регистрация прошла успешно, пожалуйста, проверьте электронную почту.
        <input type="button" class="button" value="Войти" onclick="location.href = '<?= $data['site_url'] ?>users/login'" />
    </div>
    <div class="hidden" id="error"></div>
    <form action="<?= $data['site_url'] ?>users/signup" method="post" >
        <div class="title">Логин</div>
        <div class="field"><input type="text" name="username" class="input" /></div>
        <div class="title">EMail</div>
        <div class="field"><input type="text" name="email" class="input" /></div>
        <div class="title">Пароль</div>
        <div class="field"><input type="password" name="password" class="input" /></div>
        <div class="title">Повторите пароль</div>
        <div class="field"><input type="password" name="password_rep" class="input" /></div>
        <input type="submit" class="button" value="Зарегистрироваться" />
    </form>
</div>