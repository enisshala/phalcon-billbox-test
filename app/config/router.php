<?php

//use Phalcon\Mvc\Router;

$router = $di->getRouter();
//
//// Define your routes here
//

//$router = new Phalcon\Mvc\Router();
//$router->add("/about", "About::testtAction");

//$router->add('/', array(
//    'controller' => 'orders',
//    'action' => 'testing',
//));

//return $router;
$router->handle();
