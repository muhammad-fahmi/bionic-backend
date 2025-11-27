<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Authentication Routes
$routes->post('auth', 'Auth::index');
$routes->post('auth/create', 'Auth::create');

// Frontend API
$routes->group('api', function (RouteCollection $routes) {
    $routes->get('submitted_task', 'Api::getSubmittedTask');
    $routes->get('task/(:any)', 'Api::getTask/$1');
    $routes->get('qr', 'Api::getQr');
    $routes->post('task/submit', 'Api::postTask');
});

// Admin Routes
$routes->group('admin', static function (RouteCollection $routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('m_user', 'AdminController::user');
});

$routes->set404Override(function () {
    echo view('errors/vw_custom_error');
});

//Test Route
$routes->get('test', 'Test::index');
