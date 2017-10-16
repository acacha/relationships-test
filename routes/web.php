<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Acacha\Relationships\Models\Person;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prova', function () {
    var_dump(factory(Person::class)->make());
});

Route::group(['middleware' => 'auth'], function () {
    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    Route::get('/tokens', function () {
        return view('tokens');
    });

    Route::get('/wizard','WizardController@index');

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes

    Route::get('/test/component/user-profile-photo', 'PersonProfilePhotoTestController@test');
});


