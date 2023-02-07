<?php

require './.config';
$config['emailFrom'] = $config['emailTo'];
$config['debug']     = false;


require_once '../vendor/autoload.php';
use schicksMail\schicksMail\Controller\schicksMailController;
$controller = new schicksMailController($config);
$controller->run();
