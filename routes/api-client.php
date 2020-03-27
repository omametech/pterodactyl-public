<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\Http\Middleware\Api\Client\Server\AuthenticateServerAccess;

/*
|--------------------------------------------------------------------------
| Client Control API
|--------------------------------------------------------------------------
|
| Endpoint: /api/client
|
*/
Route::get('/', 'ClientController@index')->name('api.client.index');
Route::get('/permissions', 'ClientController@permissions');

Route::group(['prefix' => '/account'], function () {
    Route::get('/', 'AccountController@index')->name('api.client.account');
    Route::get('/two-factor', 'TwoFactorController@index');
    Route::post('/two-factor', 'TwoFactorController@store');
    Route::delete('/two-factor', 'TwoFactorController@delete');

    Route::put('/email', 'AccountController@updateEmail')->name('api.client.account.update-email');
    Route::put('/password', 'AccountController@updatePassword')->name('api.client.account.update-password');

    Route::get('/api-keys', 'ApiKeyController@index');
    Route::post('/api-keys', 'ApiKeyController@store');
    Route::delete('/api-keys/{identifier}', 'ApiKeyController@delete');
});

/*
|--------------------------------------------------------------------------
| Client Control API
|--------------------------------------------------------------------------
|
| Endpoint: /api/client/servers/{server}
|
*/
Route::group(['prefix' => '/servers/{server}', 'middleware' => [AuthenticateServerAccess::class]], function () {
    Route::get('/', 'Servers\ServerController@index')->name('api.client.servers.view');
    Route::get('/websocket', 'Servers\WebsocketController')->name('api.client.servers.websocket');
    Route::get('/resources', 'Servers\ResourceUtilizationController')
        ->name('api.client.servers.resources');

    Route::post('/command', 'Servers\CommandController@index')->name('api.client.servers.command');
    Route::post('/power', 'Servers\PowerController@index')->name('api.client.servers.power');

    Route::group(['prefix' => '/databases'], function () {
        Route::get('/', 'Servers\DatabaseController@index')->name('api.client.servers.databases');
        Route::post('/', 'Servers\DatabaseController@store');
        Route::post('/{database}/rotate-password', 'Servers\DatabaseController@rotatePassword');
        Route::delete('/{database}', 'Servers\DatabaseController@delete')->name('api.client.servers.databases.delete');
    });

    Route::group(['prefix' => '/files'], function () {
        Route::get('/list', 'Servers\FileController@listDirectory')->name('api.client.servers.files.list');
        Route::get('/contents', 'Servers\FileController@getFileContents')->name('api.client.servers.files.contents');
        Route::get('/download', 'Servers\FileController@download');
        Route::put('/rename', 'Servers\FileController@renameFile')->name('api.client.servers.files.rename');
        Route::post('/copy', 'Servers\FileController@copyFile')->name('api.client.servers.files.copy');
        Route::post('/write', 'Servers\FileController@writeFileContents')->name('api.client.servers.files.write');
        Route::post('/delete', 'Servers\FileController@delete')->name('api.client.servers.files.delete');
        Route::post('/create-folder', 'Servers\FileController@createFolder')->name('api.client.servers.files.create-folder');
    });

    Route::group(['prefix' => '/schedules'], function () {
        Route::get('/', 'Servers\ScheduleController@index');
        Route::post('/', 'Servers\ScheduleController@store');
        Route::get('/{schedule}', 'Servers\ScheduleController@view');
        Route::post('/{schedule}', 'Servers\ScheduleController@update');
        Route::delete('/{schedule}', 'Servers\ScheduleController@delete');

        Route::post('/{schedule}/tasks', 'Servers\ScheduleTaskController@store');
        Route::post('/{schedule}/tasks/{task}', 'Servers\ScheduleTaskController@update');
        Route::delete('/{schedule}/tasks/{task}', 'Servers\ScheduleTaskController@delete');
    });

    Route::group(['prefix' => '/network'], function () {
        Route::get('/', 'Servers\NetworkController@index')->name('api.client.servers.network');
    });

    Route::group(['prefix' => '/users'], function () {
        Route::get('/', 'Servers\SubuserController@index');
        Route::post('/', 'Servers\SubuserController@store');
        Route::get('/{subuser}', 'Servers\SubuserController@view');
        Route::post('/{subuser}', 'Servers\SubuserController@update');
        Route::delete('/{subuser}', 'Servers\SubuserController@delete');
    });

    Route::group(['prefix' => '/settings'], function () {
        Route::post('/rename', 'Servers\SettingsController@rename');
    });
});
