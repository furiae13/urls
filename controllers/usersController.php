<?php

class usersController extends Controller
{
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->auth_user = $this->registry->get('auth_user');
        $this->model = new Users($this->registry);
        $this->view = new View($this->registry);
    }

    public function actionIndex() {}

    public function actionSignup()
    {
        if (!$this->auth_user) {
            if (!empty($_POST)) {
                try {
                    $this->model->signup($_POST, $this->view->getSiteUrl());
                    $data = array_merge($_POST, array('activation' => 1));
                    echo json_encode($data);
                    exit();
                } catch (InvalidArgumentException $e) {
                    echo json_encode(array('error' => json_decode($e->getMessage())));
                    exit();
                }
            }
            $this->view->display('users_signup_view.php', 'template_view.php');
        }
        else {
            header('Location: ' . $this->view->getSiteUrl());
            exit();
        }
    }

    public function actionActivation()
    {
        $confirmation_code = $this->registry->get('get');
        $confirmation_code = array_pop($confirmation_code);
        $data = array();
        if ($confirmation_code) {
            try {
                $this->model->activation($confirmation_code);
            } catch (InvalidArgumentException $e) {
                $data = array('error' => $e->getMessage());
            }
        }
        $this->view->display('activation_view.php', 'template_view.php', $data);
    }
    public function actionLogin()
    {
        if (!$this->auth_user) {
            if (!empty($_POST)) {
                try {
                    $this->model->login($_POST);
                    $this->model->createToken();
                    $data = array('redirect' => $this->view->getSiteUrl());
                    echo json_encode($data);
                    exit();
                } catch (InvalidArgumentException $e) {
                    echo json_encode(array('error' => json_decode($e->getMessage())));
                    exit();
                }
            }
            $this->view->display('users_view.php', 'template_view.php');
        } else {
            header('Location: ' . $this->view->getSiteUrl());
            exit();
        }

    }

    public function actionLoginApi()
    {
        if (!$this->auth_user) {
            try {
                return $this->model->login($_POST);
            } catch (InvalidArgumentException $e) {
                return false;
            }
        }
        return $this->auth_user;
    }
    public function actionLogout()
    {
        $this->model->logout();
        header('Location: ' . $this->view->getSiteUrl());
        exit();
    }

    public function actionUserAuth()
    {
        return $this->model->getUserByToken();
    }
}