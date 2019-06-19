<?php

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

$router->post('/reg','User\UserController@reg');          	//注册
$router->post('/login','User\UserController@login');		//登录
$router->post('/update','User\UserController@update');		//修改
$router->post('/weather','User\UserController@weather');  	//调用天气接口


$router->post('/decrypt','User\UserController@decrypt');    	//解密
$router->post('/decrypt1','User\UserController@decrypt1');  	//对称解密

$router->post('/decrsa','User\UserController@decrsa');  	//非对称解密  --公钥解密

$router->post('/decsign','User\UserController@decsign');  	//非对称解密  --公钥解密 签名


$router->post('app/reg','App\LoginController@reg');  	//app 注册
$router->post('app/login','App\LoginController@login');  	//app 登录
$router->get('app/center','App\LoginController@center');  	//app 登录
	


