<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index', ['filters' => 'auth']);
$routes->post('register', 'Register::index');
$routes->post('login', 'Login::index');
$routes->get('me', 'Me::index');

