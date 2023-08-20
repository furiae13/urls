<?php

class Main extends Model
{
    protected $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    public function shorten(array $data)
    {
        if (empty($data['url'])) {
            throw new InvalidArgumentException($this->registry->get('errors')['empty_url']);
        } elseif (!$this->validateUrlFormat($data['url'])) {
            throw new InvalidArgumentException($this->registry->get('errors')['url_format_error']);
        }
        return $this->createShortUrl($data['url']);
    }

    public function get($code)
    {
        return $this->existShortUrl($code, 'url');
    }

    private function validateUrlFormat($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL,FILTER_FLAG_PATH_REQUIRED);
    }

    private function getShortUrl($url)
    {
        $res =  $this->registry->get('db')->getRow('SELECT * FROM  short_urls  WHERE url = ?', array($url));
        if ($res) {
            return $res['short_code'];
        }
        return false;
    }

    private function existShortUrl($code, $return)
    {
        $res =  $this->registry->get('db')->getRow('SELECT * FROM  short_urls  WHERE short_code = ?', array($code));
        if ($res) {
            return $res[$return];
        }
        return false;
    }

    private function createShortUrl($url)
    {
        $short_code = $this->getShortUrl($url);
        if ($short_code){
            return $short_code;
        }
        $short_code = $this->createShortCode();
        if ($this->addNewRecord($url, $short_code)) {
            return $short_code;
        }
        return false;
    }

    private function addNewRecord($url, $short_code)
    {
        $res = $this->registry->get('db')->insert('INSERT INTO  short_urls  (url, short_code) VALUES (?, ?) ', array($url, $short_code));
        if ($res) {
            return $res;
        }
        throw new InvalidArgumentException($this->registry->get('errors')['add_new_error']);
    }

    private function createShortCode()
    {
        do {
            $shortCode = substr(str_shuffle($this->chars), 0, 6);
        } while ($this->existShortUrl($shortCode, 'short_code'));
        return $shortCode;
    }

    public function updateViews($code)
    {
        $this->registry->get('db')->getRow('UPDATE short_urls SET visit = visit +1  WHERE short_code = ?', array($code));
    }
}