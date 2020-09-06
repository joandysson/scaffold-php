<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__DIR__).'\config\autoload.php';
require_once dirname(__DIR__).'\config\functions.php';
require_once dirname(__DIR__).'\config\variables.php';
new \Config\Router\Router;