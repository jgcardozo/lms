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


/**
 * ROUTE FOR TESTING
 */
Route::get('test', [
    'as' => 'test',
    'uses' => 'HomeController@test'
]);

Route::get('test/fb/callback', [
    'as' => 'testcallback',
    'uses' => 'HomeController@callback'
]);

Route::get('test/fb/post', [
    'as' => 'posttofb',
    'uses' => 'HomeController@posttofb'
]);


/**
 * Front-end routes
 */
Route::get('/', [
    'as' => 'courses',
    'uses' => 'HomeController@index'
]);

Route::get('course/{course}', [
    'as' => 'single.course',
    'uses' => 'CourseController@index'
]);

Route::get('course/{course}/starter', [
    'as' => 'single.course.starter',
    'uses' => 'CourseController@starter_videos'
]);

Route::get('module/{module}', [
    'as' => 'single.module',
    'uses' => 'ModuleController@index'
]);

Route::get('lesson/{lesson}', [
    'as' => 'single.lesson',
    'uses' => 'LessonController@index'
]);

Route::group(['prefix' => 'session', 'middleware' => 'auth'], function() {

    // Mark session as completed
    Route::get('{session}/completed', [
        'as' => 'session.completed',
        'uses' => 'SessionController@complete'
    ]);
});

Route::group(['prefix' => 'user', 'middleware' => 'auth'], function()
{
    Route::get('profile', [
        'as' => 'user.profile',
        'uses' => 'UserController@profile'
    ]);

    Route::post('profile', [
        'uses' => 'UserController@store'
    ]);

    Route::get('settings', [
        'as' => 'user.settings',
        'uses' => 'UserController@settings'
    ]);

    Route::post('settings', [
        'uses' => 'UserController@settingsStore'
    ]);
});


/**
 * Infusionsoft route
 */
Route::get('is/sync', [
    'uses' => 'InfusionsoftController@sync'
]);

Route::get('is/sign', [
    'uses' => 'InfusionsoftController@signin'
]);

Route::get('is/callback', [
    'uses' => 'InfusionsoftController@callback'
]);

/**
 * Admin routes
 */
Route::get('admin/', [
	'uses' => '\Backpack\Base\app\Http\Controllers\AdminController@redirect',
	'middleware' => ['role:Administrator']
]);

Route::get('admin/dashboard', function() {
	return view('backpack::dashboard', ['title' => trans('backpack::base.dashboard')]);
});

Route::group(['prefix' => 'admin', 'middleware' => ['role:Administrator']], function()
{
    CRUD::resource('course', 'Admin\CourseCrudController');
    CRUD::resource('module', 'Admin\ModuleCrudController');
    CRUD::resource('lesson', 'Admin\LessonCrudController');
    CRUD::resource('session', 'Admin\SessionCrudController');
    CRUD::resource('resource', 'Admin\ResourceCrudController');
    CRUD::resource('coachingcall', 'Admin\CoachingCallsCrudController');
    CRUD::resource('event', 'Admin\EventCrudController');
});
Auth::routes();

Route::get('/home', 'HomeController@index');
