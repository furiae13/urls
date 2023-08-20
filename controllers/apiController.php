<?php

class ApiController extends Controller
{
    private $auth_user;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }
    public function actionIndex()
    {
        $postData = file_get_contents('php://input');
        if (!$postData) {
            return;
        }
        $_POST =  json_decode($postData, true);
        $router = $this->registry->get('router');
        $user = $router->getController('usersController', 'actionLoginApi');
        if ($user) {
            $result = array();
            if (!empty($_POST['urls'])) {
                $main = $router->getController('mainController');
                $urls = $_POST['urls'];
                if (is_array($urls)) {
                    foreach ($urls as $key => $url) {
                        $result[$key] = $this->generateShortUrl($url, $main);
                    }
                } else {
                    $result = $this->generateShortUrl($urls, $main);
                }
                echo json_encode($result);
                exit();
            }
            echo json_encode(array('error' => '401', 'message' => $this->registry->get('errors')['urls_emty']));
            exit();
        } else {
            echo json_encode(array('error' => '401', 'message' => $this->registry->get('errors')['auth_error']));
            exit();
        }
    }

    private function generateShortUrl($url, $main)
    {
        try {
            $short_code =  $main->model->shorten(array('url' => $url));
            return array('old_url' => $url, 'new_url' => $main->view->getSiteUrl() . $short_code);
        } catch (InvalidArgumentException $e) {
            echo json_encode(array('error' => $e->getMessage()));
            exit();
        }
    }
}