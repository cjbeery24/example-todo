<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    // Move routes within this middleware group if api auth is needed
});

Route::resource('todo-lists', 'Api\TodoListController')->except([
    'create',
    'edit',
]);

Route::resource('todos', 'Api\TodoController')->except([
    'create',
    'edit',
]);
