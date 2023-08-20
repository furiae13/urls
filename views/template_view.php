<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<title>Короткие ссылки</title>
</head>
<script>
	const siteUrl ="<?= $data['site_url']?>";
</script>
<link rel="stylesheet" type="text/css" href="<?= $data['site_url'] ?>css/style.css" />
<script src="<?= $data['site_url'] ?>js/js.js" type="text/javascript"></script>
<body>
<header><div class="name">Тут какое-то название</div><div class="auth_user"><?php if(!empty($data['auth_user'])) {?> Привет, <?=$data['auth_user']['username']?> <a href="<?= $data['site_url'] ?>users/logout" class="logout">(Выход)</a><?php }?></div></header>
	<?php require_once 'views/'.$content_view; ?>
</body>
</html>
