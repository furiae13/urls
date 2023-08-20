<div class="form">
    <h1>Активация</h1>
    <?php if (!empty($data['error'])): ?>
        <div class="error"><?= $data['error'] ?></div>
    <?php else:?>
        Активация прошла успешно
        <input type="button" class="button" value="Войти" onclick="location.href = '<?= $data['site_url'] ?>users/login'" />
    <?php endif; ?>
</div>