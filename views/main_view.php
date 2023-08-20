<h1>Создать ссылку</h1>
<div class="hidden" id="error"></div>
<form action="<?= $data['site_url'] ?>" method="post">
    <div class="title">Ссылка</div>
    <div class="field"><input type="text" name="url" class="input" /></div>
    <input type="submit" class="button" value="Сократить" />
</form>
<div id="result" class="hidden result">
    <div class="title">Ваша старая ссылка:</div>
    <div id="old_url"></div>
    <div class="title">Ваша короткая ссылка:</div>
    <div id="result_message"></div>
</div>