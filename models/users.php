<?php

class Users extends Model
{
    private $user_id;
    private $auth_token;
    public function login(array $login_data)
    {
        $users_fields = array(
            'username' => array('name' => 'логин', 'validators' => ['required' => 1]),
            'password' => array('name' => 'пароль', 'validators' => ['required' => 1]),
        );
        $errors = $this->validate($users_fields, $login_data);
        if (!$errors) {
            $user = $this->registry->get('db')->getRow('SELECT * FROM users WHERE username = ?', array($login_data['username']));
            if ($user === null || !password_verify($login_data['password'], $user['password'])) {
                $errors = $this->registry->get('errors')['auth_error'];
            } elseif ($user['status'] == 0) {
                $errors = $this->registry->get('errors')['login_activate_error'];
            }
        }
        if ($errors) {
            throw new InvalidArgumentException(json_encode($errors));
        }
        $user['auth_token'] = $this->refreshAuthToken();
        $this->registry->set('user', $user);
        $this->user_id = $user['id'];
        $this->auth_token = $user['auth_token'];
        $this->updateAuthToken();
        return $user;
    }

    public function signup(array $user_data, $site_url)
    {
        $users_fields = array(
            'username' => array('name' => 'логин', 'validators' => ['required' => 1, 'unique' => 'users', 'username' => 1]),
            'password' => array('name' => 'пароль', 'validators' => ['required' => 1, 'password' => 1]),
            'email' => array('name' => 'email', 'validators' => ['required' => 1, 'email' => 1, 'unique' => 'users'])
        );
        $errors = $this->validate($users_fields, $user_data);
        if (empty($user_data['password_rep']) || $user_data['password'] != $user_data['password_rep']) {
            $errors['password_rep'] = $this->registry->get('errors')['password_error'];
        }
        if ($errors) {
            throw new InvalidArgumentException(json_encode($errors));
        }
        $user_data['password'] = password_hash($user_data['password'],  PASSWORD_BCRYPT);
        $user_data['confirm_email'] = md5($user_data['email'] . time());
        $user_id = $this->addNewUser($user_data);
        if ($user_id) {
            $this->sendConfirmEmail($user_data, $site_url);
        } else {
            throw new InvalidArgumentException($this->registry->get('errors')['user_add_error']);
        }
    }

    private function validate($fields, $data)
    {
        $errors = array();
        foreach ($fields as $field_name => $field_info) {
            foreach ($field_info['validators'] as $validator => $param) {
                switch ($validator) {
                    case 'required':
                        if (empty($data[$field_name])) {
                            $errors[$field_name] = str_replace('[[name]]', $field_info['name'], $this->registry->get('errors')['empty_field']);
                        }
                        break;
                    case 'email':
                        if (!preg_match('/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/',$data[$field_name])) {
                            $errors[$field_name] = $this->registry->get('errors')['email_error'];
                        }
                        break;
                    case 'unique':
                        $issetRow = $this->registry->get('db')->getRow('SELECT * FROM '. $param .' WHERE '.$field_name.' = ?', array($data[$field_name]));
                        if ($issetRow) {
                            $errors[$field_name]  = str_replace('[[name]]', $field_info['name'], $this->registry->get('errors')['unique_field']);
                        }
                        break;
                    case 'password':
                        if (!preg_match('/(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/',$data[$field_name])) {
                            $errors[$field_name] = $this->registry->get('errors')['password_valid_error'];
                        }
                        break;
                    case 'username':
                        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$/',$data[$field_name])) {
                            $errors[$field_name] = $this->registry->get('errors')['username_error'];
                        }
                        break;
                }
            }
        }
        return $errors;
    }

    private function addNewUser($info)
    {
        $res = $this->registry->get('db')->insert('INSERT INTO users (username, email, password, confirm_email) VALUES (?, ?, ?, ?) ', array($info['username'], $info['email'], $info['password'], $info['confirm_email']));
        if ($res) {
            return $res;
        }
        return false;
    }

    public function sendConfirmEmail($user_data, $site_url)
    {
        $to = $user_data['email'];
        $subject = 'Подтверждение электронной почты';
        $message = 'Здравствуйте! <br/> <br/> Пожалуйста, подтвердите адрес вашей электронной почты. <br/> <br/> <a href="'. $site_url .'users/activation/'. $user_data['confirm_email'] .'">'. $site_url .'users/activation/'. $user_data['confirm_email'] .'</a>';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $this->registry->get('config')['email']. "\r\n";
        mail($to, $subject, $message, $headers);
    }

    public function activation($confirmation_code)
    {
        $user = $this->registry->get('db')->getRow('SELECT * FROM users WHERE confirm_email = ? AND status = 0', array($confirmation_code));
        if ($user) {
            $this->registry->get('db')->query('UPDATE users SET status = 1 WHERE confirm_email = ?', array($confirmation_code));
        } else {
            throw new InvalidArgumentException($this->registry->get('errors')['activate_error']);
        }
    }
    private function updateAuthToken()
    {
        $this->registry->get('db')->query('UPDATE users SET auth_token = ? WHERE id = ?', array($this->getAuthToken(), $this->getId()));
    }

    public function getAuthToken()
    {
        return $this->auth_token;
    }
    public function getId()
    {
        return $this->user_id;
    }
    private function refreshAuthToken()
    {
        return base64_encode(openssl_random_pseudo_bytes(32));
    }
    public function createToken()
    {
        $token = $this->getId() . ':' . $this->getAuthToken();
        setcookie('token', $token, 0, '/', '', false, true);
    }

    public function getUserByToken()
    {
        $token = !empty($_COOKIE['token']) ? $_COOKIE['token'] : '';
        if (empty($token)) {
            return null;
        }
        list($user_id, $auth_token) = explode(':', $token, 2);
        $user = $this->registry->get('db')->getRow('SELECT * FROM users WHERE id = ?', array($user_id));
        if ($user === null) {
            return null;
        }
        if ($user['auth_token'] !== $auth_token) {
            return null;
        }
        return $user;
    }

    public function getUserByEmail($email)
    {
        return $this->registry->get('db')->getRow('SELECT * FROM users WHERE email = ?', array($email));
    }

    public function logout()
    {
        $user = $this->getUserByToken();
        $this->auth_token = '';
        $this->user_id = $user['id'];
        $this->updateAuthToken();
        setcookie('token', '', time() - 1);
    }
}