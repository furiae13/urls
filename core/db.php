<?php

class DB
{
    private $db;

    public function __construct($config)
    {
        $this->db = new PDO('mysql:host=' . $config['dbhost'] . ';dbname=' . $config['dbname'], $config['dbuser'], $config['dbpassword']);
        $this->query('SET NAMES utf8');
    }
    public function query($sql, $params = array())
    {
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($params) === false) {
            return array();
        }
        return $stmt->fetchAll();
    }

    public function insert($sql, $params = array())
    {
       $stmt = $this->db->prepare($sql);
        if ($stmt->execute($params) === false) {
            return false;
        }
        return $this->db->lastInsertId();
    }

    public function update($sql, $params = array())
    {
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($params) === false) {
            return false;
        }
        return true;
    }

    public function getRow($sql, $params = array())
    {
        $query = $this->query($sql, $params);
        return array_pop($query);
    }
}