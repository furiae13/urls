<?php
class View
{
    public function __construct($registry)
    {
        $this->registry = $registry;
    }
     function display($content_view, $template_view, $data = null)
    {
        $data['auth_user'] = $this->registry->get('auth_user');
        $data['site_url'] = $this->getSiteUrl();
        require_once 'views/'.$template_view;
    }

    public function getSiteUrl()
    {
        $route = !empty($_GET['route']) ? $_GET['route'] : '';
        return sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            str_replace($route, '', $_SERVER['REQUEST_URI'])
        );
    }
}