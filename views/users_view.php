<div class="form">
    <h1>Вход</h1>
    <div class="hidden" id="error"></div>
    <form action="<?= $data['site_url'] ?>users/login" method="post">
        <div class="title">Логин</div>
        <div class="field"><input type="text" name="username" class="input" /></div>
        <div class="title">Пароль</div>
        <div class="field"><input type="password" name="password" class="input" /></div>
        <input type="submit" class="button" value="Войти" />
        <input type="button" class="button" value="Зарегистрироваться" onclick="location.href = '<?= $data['site_url'] ?>users/signup'" />
    </form>
</div>