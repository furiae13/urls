<?php

error_reporting (E_ALL);
define ('DIR_SEP', DIRECTORY_SEPARATOR);
$site_path = realpath(dirname(__FILE__)) . DIR_SEP;
define ('SITE_PATH', $site_path);
require_once(SITE_PATH . 'includes' . DIR_SEP . 'autoload.php');

spl_autoload_register('autoloader');
$config = require_once SITE_PATH.'config/config.php';
$errors = require_once SITE_PATH.'config/errors.php';
$registry = new Registry;
$registry->set('config', $config);
$registry->set ('errors', $errors);
$db = new DB($config);
$registry->set ('db', $db);
$router = new Router($registry);
$registry->set ('router', $router);
$user = $router->getController('usersController', 'actionUserAuth');
$registry->set('auth_user', $user);
$router->start();