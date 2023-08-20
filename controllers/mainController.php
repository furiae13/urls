<?php

class MainController extends Controller
{
    private $auth_user;

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->auth_user = $this->registry->get('auth_user');
        $this->model = new Main($this->registry);
        $this->view = new View($this->registry);
    }

    public function actionIndex()
    {
        if ($this->auth_user) {
            if (!empty($_POST)) {
                try {
                    $short_url = $this->model->shorten($_POST);
                    echo json_encode(array('result' => array('old' => $_POST['url'], 'new' => $this->view->getSiteUrl() . $short_url)));
                    exit();
                } catch (InvalidArgumentException $e) {
                    echo json_encode(array('error' => $e->getMessage()));
                    exit();
                }
            }
            $this->view->display('main_view.php', 'template_view.php');
        } else {
            header('Location: ' . $this->view->getSiteUrl().'users/login');
            exit();
        }
    }

    public function actionGetUrl($short_code)
    {
        $url = $this->model->get($short_code);
        if ($url) {
            $this->model->updateViews($short_code);
            header('Location: ' . $url);
            exit();
        }
        $this->view->display('error_view.php', 'template_view.php');
    }

    public function actionApi()
    {

    }
}