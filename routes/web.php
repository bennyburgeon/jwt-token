<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
    ////// company verification////
    $router->post('otp-send', 'AuthController@otpSend');
    $router->post('otp-verification', 'AuthController@otpVerification');
    $router->post('set-login', 'AuthController@setUsernamePassword');

    $router->post('forgot-password', 'AuthController@forgotPassword');
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
    $router->post('me', 'AuthController@me');

    $router->group(['middleware' => 'auth' ], function ($router) {
        
        $router->post('change-password', 'CompanyController@change_password');
        ///// super Admin //////
        $router->post('register-company', 'CompanyController@store');
        $router->get('company-list', 'CompanyController@list');
        $router->get('company-view/{id}', 'CompanyController@view');
        $router->post('company-update', 'CompanyController@update');
        $router->post('company-delete', 'CompanyController@delete');
        ///// company verification//////
        
        ///// profile //////
        $router->get('dashboard', 'CompanyController@dashboard');
        $router->get('profile-view', 'CompanyController@profileView');
        $router->post('profile-edit', 'CompanyController@profileEdit');
        ////////
        $router->post('register-customer', 'CustomerController@store');
        $router->get('customer-list', 'CustomerController@list');
        $router->get('customer-view/{id}', 'CustomerController@view');
        $router->post('customer-update', 'CustomerController@update');
        $router->post('customer-delete', 'CustomerController@delete');
    
        $router->post('register-product', 'ProductController@store');
        $router->get('product-list', 'ProductController@list');
        $router->get('product-view/{id}', 'ProductController@view');
        $router->post('product-update', 'ProductController@update');
        $router->post('product-delete', 'ProductController@delete');
    
        $router->post('register-invoice', 'InvoiceController@store');
        $router->get('invoice-list', 'InvoiceController@list');
        $router->get('invoice-view/{id}', 'InvoiceController@view');
        $router->get('invoice-view-details/{id}', 'InvoiceController@view_details');
        $router->post('invoice-update', 'InvoiceController@update');
        $router->post('invoice-delete', 'InvoiceController@delete');
        $router->get('export-pdf/{id}', 'InvoiceController@exportPdf');
    });

});