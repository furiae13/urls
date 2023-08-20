<?php

Class Router
{
    private $registry;
    function __construct($registry) {
        $this->registry = $registry;
    }
    public function start()
    {
        $controller_name = 'main';
        $action_name = 'index';
        $routes = explode('/', empty($_GET['route']) ? '' : $_GET['route']);
        if ( !empty($routes[0]) ) {
            $controller_name = $routes[0];
        }
        if ( !empty($routes[1]) ) {
            $action_name = $routes[1];
        }
        if (count($routes) > 2) {
            $get = array();
            for ($i = 2; $i < count($routes); $i++) {
                $get[] = $routes[$i];
            }
            $this->registry->set('get', $get);
        }
        $param = $controller_name;
        $controller_name = strtolower($controller_name).'Controller';
        $action_name = 'action'.ucfirst($action_name);
        if ($this->getController($controller_name, $action_name) === false) {
            $this->getController('mainController', 'actionGetUrl', $param);
        }
    }

    public function getController($controller, $action = null, $param = null)
    {
        $controller_path = SITE_PATH.'controllers/' . $controller. '.php';
        $model_path = SITE_PATH.'models/' . str_replace('Controller', '', $controller) . '.php';
        if (!is_readable($controller_path)) {
            return false;
        }
        if(file_exists($model_path)) {
            require_once  $model_path;
        }
        require_once  $controller_path;
        $controller = new $controller($this->registry);
        if ($action) {
            if (!is_callable(array($controller, $action))) {
                return false;
            }
            return $controller->$action($param);
        } else {
            return $controller;
        }
    }
}