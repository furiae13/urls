<?php

function autoloader($class_name) {

    $filename = strtolower($class_name) . '.php';
    $file = SITE_PATH . 'core' . DIR_SEP . $filename;
    if (file_exists($file) == false) {
        return false;
    }
    require_once ($file);
}
