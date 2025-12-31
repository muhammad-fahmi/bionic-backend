<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// Authentication Routes
$routes->group("auth", static function (RouteCollection $routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login_handler');
    $routes->post('create', 'Auth::create');
});

// Admin Routes
$routes->group('admin', static function (RouteCollection $routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->group('manage', static function (RouteCollection $routes) {
        // Management User
        $routes->group('user', static function (RouteCollection $routes) {
            $routes->get('/', 'Admin\User::index');
            $routes->post('add', 'Admin\User::create');
            $routes->get('(:num)', 'Admin\User::show/$1');
            $routes->put('edit/(:num)', 'Admin\User::update/$1');
            $routes->delete('delete/(:num)', 'Admin\User::delete/$1');
        });

        // Management Item
        // TODO: Perbaiki Module Management Item
        $routes->group('item', static function (RouteCollection $routes) {
            $routes->get('/', 'Admin\Item::index');
            $routes->post('modal', 'Admin\Item::modal');
            $routes->post('add', 'Admin\Item::add');
            $routes->get('(:num)', 'Admin\Item::show/$1');
            $routes->put('edit/(:num)', 'Admin\Item::update/$1');
            $routes->delete('delete/(:num)', 'Admin\Item::delete/$1');
        });

        // Management Activity
        // TODO: Perbaiki Module Management Activity dan Tambahkan di sidebar
        $routes->group('activity', static function (RouteCollection $routes) {
            $routes->get('/', 'Admin\Activity::index');
        });

        // TODO: Perbaiki Module Management Lokasi
        $routes->group('location', static function (RouteCollection $routes) {
            $routes->get('/', 'Admin\Location::index');
        });

        // TODO: Sesuaikan Module Management Shift
        $routes->group('shift', static function (RouteCollection $routes) {
            $routes->get('/', 'Admin\Shift::index');
            $routes->post('initialize', 'Admin\Shift::initialize');
            $routes->post('rotate-all', 'Admin\Shift::rotateAll');
            $routes->post('assign-user', 'Admin\Shift::assignUser');
            $routes->get('history/(:num)', 'Admin\Shift::history/$1');
            $routes->get('statistics', 'Admin\Shift::getStatistics');
        });

        // TODO: Tambahkan Module Task ke Sidebar
        $routes->group('task', static function (RouteCollection $routes) {
            $routes->get('/', 'Admin\TaskManagement::index');
        });
    });
});

// TODO: Perbaiki Module Operator/Petugas
$routes->group("operator", static function (RouteCollection $routes) {
    $routes->get('/', 'Operator::index', ['as' => 'operator.index']);
});

// TODO: Perbaiki Module Verifikator
$routes->group("verifikator", static function (RouteCollection $routes) {
    $routes->get('/', 'Verifikator::index', ['as' => 'verifikator.index']);
});

$routes->set404Override(function () {
    echo view('errors/vw_custom_error');
});

// Redirect
$routes->addRedirect("/", "auth/login");