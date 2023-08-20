<?php

abstract class Controller
{
    protected $registry;
    public $model;
    public $view;
    function __construct($registry) {
        $this->registry = $registry;
        $this->view = new View($this->registry);
    }
    abstract function actionIndex();
}