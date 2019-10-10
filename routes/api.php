<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
], function($api) {
    $api->group([
       'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {
        //邮箱验证码
        $api->post('verifyCodes', 'VerifyController@store')
            ->name('api.verificationCodes.store');
        //用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
    }); 
    
    $api->group([
       'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function($api) {
        //用户修改
        $api->patch('users{user}','UsersController@update')
            ->name('api.users.udate');
        //用户登录
        $api->post('login','SessionsController@store')
            ->name('api.sessions.login');
        //用户登出
        $api->delete('logout','SessionsController@destroy')
            ->name('api.sessions.logout');
        //重置密码的邮箱发送页面
        $api->get('password/reset', 'ForgotPasswordController@showLinkRequestForm')
            ->name('api.password.request');
        //邮箱发送重设链接
         $api->post('password/email', 'ForgotPasswordController@sendResetLinkEmail')
            ->name('api.password.email');
         //密码更新页面
         $api->get('password/reset/{token}', 'ResetPasswordController@showResetForm')
             ->name('api.password.reset');
         //执行密码更新操作
         $api->post('password/reset', 'ResetPasswordController@reset')
             ->name('api.password.update');
    });
});




/*
Route::get('/users', 'UsersController@index')->name('users.index');
Route::get('/users/create', 'UsersController@create')->name('users.create');
Route::get('/users/{user}', 'UsersController@show')->name('users.show');
Route::post('/users', 'UsersController@store')->name('users.store');
Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');
Route::patch('/users/{user}', 'UsersController@update')->name('users.update');
Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');


Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');
 */